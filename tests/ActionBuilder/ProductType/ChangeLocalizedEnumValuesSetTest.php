<?php

namespace BestIt\CommercetoolsODM\Tests\ActionBuilder\ProductType;

use BestIt\CommercetoolsODM\ActionBuilder\ProductType\ChangeLocalizedEnumValuesSet;
use BestIt\CommercetoolsODM\ActionBuilder\ProductType\ProductTypeActionBuilder;
use BestIt\CommercetoolsODM\Mapping\ClassMetadataInterface;
use BestIt\CommercetoolsODM\Tests\ActionBuilder\SupportTestTrait;
use Commercetools\Core\Model\ProductType\ProductType;
use Commercetools\Core\Request\ProductTypes\Command\ProductTypeAddLocalizedEnumValueAction;
use Commercetools\Core\Request\ProductTypes\Command\ProductTypeChangeLocalizedEnumLabelAction;
use Commercetools\Core\Request\ProductTypes\Command\ProductTypeRemoveEnumValuesAction;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Test for localized enum set action builder
 *
 * @author Michel Chowanski <michel.chowanski@bestit-online.de>
 * @package BestIt\CommercetoolsODM\Tests\ActionBuilder\ProductType
 */
class ChangeLocalizedEnumValuesSetTest extends TestCase
{
    use SupportTestTrait;

    /**
     * The test class.
     *
     * @var ChangeLocalizedEnumValuesSet|PHPUnit_Framework_MockObject_MockObject
     */
    protected $fixture = null;

    /**
     * Returns an array with the assertions for the support method.
     *
     * The First Element is the field path, the second element is the reference class and the optional third value
     * indicates the return value of the support method.
     *
     * @return array
     */
    public function getSupportAssertions(): array
    {
        return [
            ['attributes/1/type/elementType/values', ProductType::class, true],
            ['attributes/1/type/values', ProductType::class],
            ['attributes', ProductType::class],
        ];
    }

    /**
     * Sets up the test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->fixture = new ChangeLocalizedEnumValuesSet();
    }

    /**
     * Test with wrong enum type (instead of lenum)
     *
     * @return void
     */
    public function testWrongType()
    {
        $productType = new ProductType([
            'attributes' => [
                ['name' => 'Denios', 'type' => ['name' => 'set', 'elementType' => ['name' => 'lenum']]],
                ['name' => 'Denios 2', 'type' => ['name' => 'set', 'elementType' => ['name' => 'enum']]],
            ],
        ]);

        $oldData = [
            'attributes' => [
                ['type' => ['name' => 'set', 'elementType' => ['name' => 'lenum']]],
                ['type' => ['name' => 'set', 'elementType' => ['name' => 'enum']]],
            ],
        ];

        $this->fixture->setLastFoundMatch([uniqid(), 1]);
        $actions = $this->fixture->createUpdateActions(
            [],
            $this->createMock(ClassMetadataInterface::class),
            [],
            $oldData,
            $productType
        );

        static::assertCount(0, $actions);
    }

    /**
     * Test with new value
     *
     * @return void
     */
    public function testNewValue()
    {
        $productType = new ProductType([
            'attributes' => [
                [
                    'name' => $attributeName = uniqid(),
                    'type' => [
                        'name' => 'set',
                        'elementType' => [
                            'name' => 'lenum',
                            'values' => [
                                $changedValue = ['key' => 'foo', 'label' => ['de' => 'bar']],
                                ['key' => 'denios', 'label' => ['de' => 'Denios']],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $oldData = [
            'attributes' => [
                [
                    'name' => $attributeName,
                    'type' => [
                        'name' => 'set',
                        'elementType' =>         [
                            'name' => 'lenum',
                            'values' => [
                                ['key' => 'denios', 'label' => ['de' => 'Denios']],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->fixture->setLastFoundMatch([uniqid(), 0]);
        $actions = $this->fixture->createUpdateActions(
            [$changedValue],
            $this->createMock(ClassMetadataInterface::class),
            [],
            $oldData,
            $productType
        );

        $action = $actions[0];
        assert($action instanceof ProductTypeAddLocalizedEnumValueAction);

        static::assertCount(1, $actions);
        static::assertSame($attributeName, $action->getAttributeName());
        static::assertEquals($changedValue, $action->getValue()->toArray());
    }

    /**
     * Test with changed label
     *
     * @return void
     */
    public function testChangedLabel()
    {
        $productType = new ProductType([
            'attributes' => [
                [
                    'name' => $attributeName = uniqid(),
                    'type' => [
                        'name' => 'set',
                        'elementType' => [
                            'name' => 'lenum',
                            'values' => [
                                $changedValue = ['key' => 'foo', 'label' => ['de' => 'bar']],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $oldData = [
            'attributes' => [
                [
                    'name' => $attributeName,
                    'type' => [
                        'name' => 'set',
                        'elementType' => [
                            'name' => 'lenum',
                            'values' => [
                                ['label' => ['de' => 'Denios'], 'key' => 'foo'],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->fixture->setLastFoundMatch([uniqid(), 0]);
        $actions = $this->fixture->createUpdateActions(
            [$changedValue, ['label' => 'FOO'], ['key' => 'BAR']],
            $this->createMock(ClassMetadataInterface::class),
            [],
            $oldData,
            $productType
        );

        $action = $actions[0];
        assert($action instanceof ProductTypeChangeLocalizedEnumLabelAction);

        static::assertCount(1, $actions);
        static::assertSame($attributeName, $action->getAttributeName());
        static::assertEquals($changedValue, $action->getNewValue()->toArray());
    }

    /**
     * Test remove value
     *
     * @return void
     */
    public function testRemoveValue()
    {
        $productType = new ProductType([
            'attributes' => [
                [
                    'name' => $attributeName = uniqid(),
                    'type' => [
                        'name' => 'set',
                        'elementType' => [
                            'name' => 'lenum',
                            'values' => [
                                ['key' => 'denios', 'label' => ['de' => 'Denios']],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $oldData = [
            'attributes' => [
                [
                    'name' => $attributeName,
                    'type' => [
                        'name' => 'set',
                        'elementType' => [
                            'name' => 'lenum',
                            'values' => [
                                ['key' => 'foo', 'label' => ['de' => 'bar']],
                                ['key' => 'denios', 'label' => ['de' => 'Denios']],
                                ['key' => 999, 'label' => ['de' => 'foo']],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->fixture->setLastFoundMatch([uniqid(), 0]);
        $actions = $this->fixture->createUpdateActions(
            [],
            $this->createMock(ClassMetadataInterface::class),
            [],
            $oldData,
            $productType
        );

        $action = $actions[0];
        assert($action instanceof ProductTypeRemoveEnumValuesAction);

        static::assertCount(1, $actions);
        static::assertSame($attributeName, $action->getAttributeName());
        static::assertEquals(['foo', '999'], $action->getKeys());

        foreach ($action->getKeys() as $key) {
            static::assertInternalType('string', $key, "Key {$key} is not a string");
        }
    }

    /**
     * Checks the instance of the builder.
     *
     * @return void
     */
    public function testInstance()
    {
        static::assertInstanceOf(ProductTypeActionBuilder::class, $this->fixture);
    }
}
