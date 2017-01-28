<?php
namespace FluidTYPO3\Builder\Tests\Unit\Property;

use FluidTYPO3\Builder\Property\FormTypeConverter;
use FluidTYPO3\Flux\Form;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfiguration;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Claus Due <claus@namelesscoder.net>
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
 ***************************************************************/

/**
 * Class FormTypeConverterTest
 */
class FormTypeConverterTest extends UnitTestCase
{

    /**
     * Note about test: although this test does test much more
     * than just the "subject under test" (e.g. the entire
     * feature set contained in that subject's output object)
     * it is required for consistency, since the forms also
     * contained in this project which can generate the array,
     * depend on the input giving the expected output.
     *
     * Any differences that may be introduced by Flux must
     * therefore be caught here.
     *
     * @test
     */
    public function testCreatesExpectedFormInstance()
    {
        $structure = [
            'id' => 'test',
            'label' => 'Some form',
            'sheets' => [
                'default' => [
                    'label' => 'Sheet label',
                    'description' => 'Sheet description',
                    'shortDescription' => 'Short description',
                    'fields' => [
                        'test' => [
                            'type' => 'Input',
                            'label' => 'Test field',
                            'default' => 'default value',
                            'placeholder' => 'Placeholder text',
                            'wizards' => [
                                'link' => [
                                    'type' => 'Link'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $expected = [
            'sheets' => [
                'default' => [
                    'ROOT' => [
                        'type' => 'array',
                        'el' => [
                            'test' => [
                                'label' => 'Test field',
                                'exclude' => 1,
                                'config' => [
                                    'type' => 'input',
                                    'transform' => null,
                                    'default' => 'default value',
                                    'placeholder' => 'Placeholder text',
                                    'size' => 32,
                                    'max' => null,
                                    'eval' => null,
                                    'wizards' => [
                                        'link' => [
                                            'type' => 'popup',
                                            'title' => 'link',
                                            'icon' => 'link_popup.gif',
                                            'hideParent' => 0,
                                            'module' => [
                                                'name' => 'wizard_element_browser',
                                                'urlParameters' => [
                                                    'mode' => 'wizard',
                                                    'act' => 'file'
                                                ]
                                            ],
                                            'JSopenParams' => 'height=500,width=400,status=0,menubar=0,scrollbars=1',
                                            'params' => [
                                                'blindLinkOptions' => '',
                                                'blindLinkFields' => '',
                                                'allowedExtensions' => ''
                                            ]
                                        ]
                                    ]
                                ],
                                'displayCond' => null
                            ]
                        ],
                        'sheetTitle' => 'Sheet label',
                        'sheetDescription' => 'Sheet description',
                        'sheetShortDescr' => 'Short description'
                    ]
                ]
            ],
            'meta' => [
                'langDisable' => 1,
                'langChildren' => 0
            ]
        ];
        $converter = GeneralUtility::makeInstance(ObjectManager::class)->get(FormTypeConverter::class);
        $form = $converter->convertFrom($structure, Form::class, [], $this->getMockBuilder(PropertyMappingConfiguration::class)->getMock());
        $this->assertEquals($expected, $form->build());
    }


}
