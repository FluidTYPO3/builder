<?php
namespace FluidTYPO3\Builder\Analysis\Fluid;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Claus Due <claus@namelesscoder.net>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

use FluidTYPO3\Builder\Analysis\Metric;
use FluidTYPO3\Builder\Analysis\MessageInterface;
use FluidTYPO3\Builder\Analysis\Fluid\Message\UncompilableMessage;
use FluidTYPO3\Builder\Parser\ExposedTemplateCompiler;
use FluidTYPO3\Builder\Parser\ExposedTemplateParser;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Fluid\Core\Parser\ParsedTemplateInterface;
use TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\NodeInterface;
use TYPO3\CMS\Fluid\Core\Parser\SyntaxTree\ViewHelperNode;
use TYPO3\CMS\Fluid\Core\Rendering\RenderingContext;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractConditionViewHelper;
use TYPO3\CMS\Fluid\ViewHelpers\SectionViewHelper;
use TYPO3Fluid\Fluid\Core\Compiler\TemplateCompiler;

/**
 * Metrics: Fluid Template Node Counter
 *
 * Tallies nodes used in a Fluid template, yielding metrics
 * information about each template file to help you optimise.
 *
 * Metrics parameters explained:
 *
 * METRIC_TOTAL_SPLITS is a measure of how many dynamic tags
 * were used in your template. Each split represents one token
 * point which Fluid must process individually - keeping this
 * number low is an obvious way to improve performance.
 *
 * METRIC_TOTAL_NODES is a measure of how many Fluid nodes you
 * used - for example, each variable accessed and each ViewHelper
 * called. Usually corresponds with METRIC_TOTAL_SPLITS, but a
 * divergence could indicate unnecessary splits being performed
 * due to the non-Fluid content in your template.
 *
 * METRIC_CONDITION_NODES measures how many conditions you used
 * in your template. Since use of conditions causes the compiled
 * Fluid code to contain one full block of compiled PHP code per
 * case in each condition, keeping this number low is vital to
 * reducing the METRIC_CACHED_SIZE of the template, thus greatly
 * reducing the time it takes to process the compiled template.
 * A high value in this metric combined with a high cached size
 * indicates that your condition blocks contain many nodes which
 * could/should be moved to Partials to improve performance.
 *
 * METRIC_MAXIMUM_ARGUMENT_COUNT measures arguments used for all
 * ViewHelpers in the template, recording the maximum number of
 * arguments. A high value here indicates the use of a complex
 * ViewHelper which may be possible to improve by creating a
 * custom version of said ViewHelper. The class name of the
 * ViewHelper for which you used the most arguments, is recorded
 * as payload for the metric.
 *
 * METRIC_CACHED_SIZE simply measures how big the compiled PHP
 * code for the specific template file will be. For example, all
 * ViewHelper nodes used and especially conditions (see reason
 * above) will increase this size. A low value is desirable and
 * indicates a low complexity of your template, but does not
 * necessarily indicate how fast your template renders - it does
 * however indicate how fast the template can be re-initialized
 * from a cached state. Keep in mind: even very small Fluid
 * templates may result in very big compiled code!
 *
 * METRIC_SECTIONS measures how many sections you used. A fairly
 * low number is preferred - but the more important metric in
 * determining how well the template will perform, is the metric
 * which records how many nodes on average are used per section.
 * Since sections are cached individually, an reasonably low
 * average nodes per section is desirable.
 *
 * METRIC_NODES_PER_SECTION_AVERAGE measures the average number
 * of Fluid nodes (variables, ViewHelpers) used per section. The
 * farther this number is from METRIC_NODES_PER_SECTION_MAXIMUM,
 * the more likely it is that one or more of your sections are
 * too complex compared to others.
 *
 * METRIC_NODES_PER_SECTION_MAXIMUM measures the overall maximum
 * number of nodes in the most populated section in the template.
 * Keeping this number low usually means your templates compile
 * to more efficient code (keeping in mind the conditions above).
 *
 * METRIC_VIEWHELPERS measures how many actual ViewHelpers you
 * use. The higher this number is, the slower your template will
 * be to render. It follows logically that ViewHelpers are the
 * most inefficient template parts to process - reducing this
 * number, for example by preparing template variables, usually
 * results in a faster template rendering.
 *
 * METRIC_MAXIMUM_NESTING_LEVEL is a very important metric which
 * measures the maximum depth of nested ViewHelpers, for example
 * an f:section with an f:if and an f:then inside, results in an
 * immediate value of "3" being recorded (section, if and then).
 * The higher this value is, the more classes will need to be
 * instanciated to render the template completely. A low value
 * here is naturally desirable - as low as possible, in fact.
 */
class NodeCounter
{

    const METRIC_TOTAL_SPLITS = 'SplitsTotal';
    const METRIC_TOTAL_NODES = 'NodesTotal';
    const METRIC_CONDITION_NODES = 'ConditionsTotal';
    const METRIC_MAXIMUM_ARGUMENT_COUNT = 'MaxArguments';
    const METRIC_NODES_PER_SECTION_AVERAGE = 'NodesPerSectionAverage';
    const METRIC_NODES_PER_SECTION_MAXIMUM = 'NodesPerSectionMaximum';
    const METRIC_CACHED_SIZE = 'CachedSize';
    const METRIC_SECTIONS = 'SectionNodes';
    const METRIC_VIEWHELPERS = 'ViewHelperNodes';
    const METRIC_MAXIMUM_NESTING_LEVEL = 'MaxNestingLevel';

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     * @return void
     */
    public function injectObjectManager(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Threshold values causing different severities; [$ok, $notice, $warning).
     * Filled by initializeObject using initial values of $this->metrics just below.
     *
     * @var array
     */
    protected $thresholds = [];

    /**
     * @var Metric[]
     */
    protected $metrics = [
        self::METRIC_TOTAL_SPLITS => [150, 300],
        self::METRIC_TOTAL_NODES => [100, 250],
        self::METRIC_VIEWHELPERS => [50, 100],
        self::METRIC_SECTIONS => [8, 15],
        self::METRIC_CONDITION_NODES => [10, 25],
        self::METRIC_NODES_PER_SECTION_AVERAGE => [8, 15],
        self::METRIC_NODES_PER_SECTION_MAXIMUM => [15, 30],
        self::METRIC_CACHED_SIZE => [250, 500],
        self::METRIC_MAXIMUM_ARGUMENT_COUNT => [8, 12],
        self::METRIC_MAXIMUM_NESTING_LEVEL => [15, 25],
    ];

    /**
     * @return void
     */
    public function initializeObject()
    {
        foreach ($this->metrics as $metricName => $thresholds) {
            $this->metrics[$metricName] = $this->objectManager->get(Metric::class)->setName($metricName)->setValue(0);
            $this->thresholds[$metricName] = $thresholds;
        }
    }

    /**
     * @param ExposedTemplateParser $parser
     * @param mixed $parsedTemplate
     * @return Metric[]
     */
    public function count(ExposedTemplateParser $parser, $parsedTemplate)
    {
        $method = new \ReflectionMethod($parser, 'buildObjectTree');
        $method->setAccessible(true);
        $splitTemplate = $parser->getSplitTemplate();
        $parsingState = $method->invokeArgs($parser, [$splitTemplate, new RenderingContext()]);
        $objectTree = $parsingState->getRootNode()->getChildNodes();
        try {
            if (false === $parsedTemplate->isCompilable()) {
                $this->get(self::METRIC_CACHED_SIZE)->setValue(0)->addMessage(new UncompilableMessage());
            } else {
                /** @var ExposedTemplateCompiler $compiler */
                $compiler = $this->getTemplateCompiler();
                if (method_exists($compiler, 'store')) {
                    $code = $compiler->store('testcompile_' . sha1(microtime(true)), $parsingState);
                } else {
                    $code = $compiler->compile($parsingState);
                }
                $this->set(self::METRIC_CACHED_SIZE, round(mb_strlen($code) / 1024, 1));
            }
        } catch (\TYPO3Fluid\Fluid\Core\StopCompilingException $error) {
            $this->get(self::METRIC_CACHED_SIZE)->setValue(0)->addMessage(new UncompilableMessage());
        }
        // traversals, cumulatively increments $this->nodeCounter values.
        $this->determineTotalNodeCount($objectTree);
        $this->determineMaximumArgumentCount($objectTree);
        $this->determineMaximumNestingLevel($objectTree);
        $this->analyzePossibleSectionNodes($objectTree);
        // counting, integers which we set directly into $this->nodeCounter values.
        $this->set(self::METRIC_TOTAL_SPLITS, count($splitTemplate));
        $this->evaluate();
        return $this->metrics;
    }

    /**
     * @return ExposedTemplateCompiler
     */
    protected function getTemplateCompiler()
    {
        if ($this->assertCoreVersionAtLeast(8)) {
            $compiler = new TemplateCompiler();
            $compiler->setRenderingContext(new RenderingContext());
            return $compiler;
        }
        return $this->objectManager->get('FluidTYPO3\Builder\Parser\ExposedTemplateCompiler');
    }

    /**
     * @param string $counter
     * @param mixed $value
     * @return NodeCounter
     */
    public function set($counter, $value)
    {
        $this->metrics[$counter]->setValue($value);
        return $this;
    }

    /**
     * @param string $counter
     * @return Metric
     */
    public function get($counter)
    {
        return $this->metrics[$counter];
    }

    /**
     * @return MessageInterface[]
     */
    public function getMessages()
    {
        /** @var MessageInterface[] $messages */
        $messages = [];
        foreach ($this->metrics as $metric) {
            array_merge($messages, $metric->getMessages());
        }
        return $messages;
    }

    /**
     * @param NodeInterface[] $nodes
     * @return void
     */
    protected function analyzePossibleSectionNodes($nodes)
    {
        $sectionNodeCounts = [];
        foreach ($nodes as $node) {
            if (true === $node instanceof ViewHelperNode) {
                /** @var ViewHelperNode $node */
                $instance = $node->getUninitializedViewHelper();
                if (true === $instance instanceof SectionViewHelper) {
                    array_push($sectionNodeCounts, $this->countNodesRecursive($node->getChildNodes()));
                }
            }
        }
        if (0 < count($sectionNodeCounts)) {
            $this->set(self::METRIC_NODES_PER_SECTION_MAXIMUM, max($sectionNodeCounts));
            $this->set(
                self::METRIC_NODES_PER_SECTION_AVERAGE,
                array_sum($sectionNodeCounts) / count($sectionNodeCounts)
            );
        }
    }

    /**
     * @param NodeInterface[] $nodes
     * @return integer
     */
    protected function countNodesRecursive($nodes)
    {
        $count = 0;
        foreach ($nodes as $node) {
            ++ $count;
            $this->countNodesRecursive($node->getChildNodes());
        }
        return $count;
    }

    /**
     * @param NodeInterface[] $nodes
     * @return void
     */
    protected function determineTotalNodeCount($nodes)
    {
        foreach ($nodes as $node) {
            $numberOfChildNodes = $this->determineTotalNodeCount($node->getChildNodes());
            // increment: count this node and its child nodes
            $this->get(self::METRIC_TOTAL_NODES)->increment(1 + $numberOfChildNodes);
            if (true === $node instanceof ViewHelperNode) {
                /** @var ViewHelperNode $node */
                $this->get(self::METRIC_VIEWHELPERS)->increment();
                $instance = $node->getUninitializedViewHelper();
                if (true === $instance instanceof AbstractConditionViewHelper) {
                    $this->get(self::METRIC_CONDITION_NODES)->increment();
                } elseif (true === $instance instanceof SectionViewHelper) {
                    $this->get(self::METRIC_SECTIONS)->increment();
                }
                $arguments = $node->getArguments();
                $numberOfArgumentNodes = $this->determineTotalNodeCount($arguments);
                $this->get(self::METRIC_TOTAL_NODES)->increment($numberOfArgumentNodes);
            }
        }
    }

    /**
     * @param NodeInterface[] $nodes
     * @param integer $level
     * @return void
     */
    protected function determineMaximumNestingLevel($nodes, $level = 0)
    {
        $this->get(self::METRIC_MAXIMUM_NESTING_LEVEL)->setOnlyIfHigher($level);
        foreach ($nodes as $node) {
            $this->determineMaximumNestingLevel($node->getChildNodes(), $level + 1);
        }
    }

    /**
     * @param NodeInterface[] $nodes
     * @return void
     */
    protected function determineMaximumArgumentCount($nodes)
    {
        foreach ($nodes as $node) {
            if (true === $node instanceof ViewHelperNode) {
                /** @var ViewHelperNode $node */
                $arguments = $node->getArguments();
                $this->get(self::METRIC_MAXIMUM_ARGUMENT_COUNT)->setOnlyIfHigher(count($arguments));
                $this->determineMaximumArgumentCount($arguments);
            }
            $this->determineMaximumArgumentCount($node->getChildNodes());
        }
    }

    /**
     * Evaluates all collected Metrics against defined
     * threshold values, adding messages as needed.
     *
     * @return void
     */
    public function evaluate()
    {
        /** @var MessageInterface $message */
        foreach ($this->metrics as $metricName => $metric) {
            list ($notice, $warning) = $this->thresholds[$metricName];
            $value = $metric->getValue();
            if ($value <= $notice) {
                $message = $this->objectManager->get('FluidTYPO3\Builder\Analysis\OkMessage');
            } elseif ($value <= $warning) {
                $message = $this->objectManager->get('FluidTYPO3\Builder\Analysis\NoticeMessage');
            } else {
                $message = $this->objectManager->get('FluidTYPO3\Builder\Analysis\WarningMessage');
            }
            $message->setPayload(array_merge([$value], $this->thresholds[$metricName]));
            $metric->addMessage($message);
        }
    }

    /**
     * @param integer $majorVersion
     * @param integer $minorVersion
     * @return boolean
     */
    protected function assertCoreVersionAtLeast($majorVersion, $minorVersion = 0)
    {
        list ($major, $minor, ) = explode('.', TYPO3_version);
        return ($major >= $majorVersion && $minor >= $minorVersion);
    }
}
