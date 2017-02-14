<?php

namespace BestIt\CommercetoolsODM\Tests\ActionBuilder\Product;

use BestIt\CommercetoolsODM\ActionBuilder\Product\ProductActionBuilder;
use BestIt\CommercetoolsODM\ActionBuilder\Product\SetTaxCategory;
use BestIt\CommercetoolsODM\Mapping\ClassMetadataInterface;
use BestIt\CommercetoolsODM\Tests\ActionBuilder\SupportTestTrait;
use Commercetools\Core\Model\Product\Product;
use Commercetools\Core\Request\Products\Command\ProductSetTaxCategoryAction;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Checks if the tax category is set.
 * @author blange <lange@bestit-online.de>
 * @package BestIt\CommercetoolsODM
 * @subpackage ActionBuilder\Product
 * @version $id$
 */
class SetTaxCategoryTest extends \PHPUnit_Framework_TestCase
{
    use SupportTestTrait;

    /**
     * The test class.
     * @var SetTaxCategory|PHPUnit_Framework_MockObject_MockObject
     */
    protected $fixture = null;

    /**
     * Returns an array with the assertions for the upport method.
     *
     * The First Element is the field path, the second element is the reference class and the optional third value
     * indicates the return value of the support method.
     * @return array
     */
    public function getSupportAssertions(): array
    {
        return [
            ['taxCategory', Product::class, true],
            ['/taxCategory', Product::class],
            ['/taxCategory/', Product::class],
            ['taxCategory/', Product::class],
        ];
    }

    /**
     * Sets up the test.
     * @return void
     */
    public function setUp()
    {
        $this->fixture = new SetTaxCategory();
    }

    /**
     * Checks if the tax category can be removed.
     * @covers SetTaxCategory::createUpdateActions()
     * @return void
     */
    public function testCreateUpdateActionsEmpty()
    {
        $actions = $this->fixture->createUpdateActions(
            [],
            static::createMock(ClassMetadataInterface::class),
            [],
            [],
            new Product()
        );

        /** @var $action ProductSetTaxCategoryAction */
        static::assertCount(1, $actions);
        static::assertInstanceOf(ProductSetTaxCategoryAction::class, $action = $actions[0]);
        static::assertNull($action->getTaxCategory());
    }

    /**
     * Checks if the tax category can be changed.
     * @covers SetTaxCategory::createUpdateActions()
     * @return void
     */
    public function testCreateUpdateActionsFilled()
    {
        $actions = $this->fixture->createUpdateActions(
            [
                'id' => $taxCategoryId = uniqid()
            ],
            static::createMock(ClassMetadataInterface::class),
            [],
            [],
            new Product()
        );

        /** @var $action ProductSetTaxCategoryAction */
        static::assertCount(1, $actions);
        static::assertInstanceOf(ProductSetTaxCategoryAction::class, $action = $actions[0]);
        static::assertSame($taxCategoryId, $action->getTaxCategory()->getId());
    }

    /**
     * Checks the instance of the builder.
     * @return void
     */
    public function testInstance()
    {
        static::assertInstanceOf(ProductActionBuilder::class, $this->fixture);
    }
}
