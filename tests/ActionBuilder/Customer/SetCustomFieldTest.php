<?php

namespace BestIt\CommercetoolsODM\Tests\ActionBuilder\ProductType;

use BestIt\CommercetoolsODM\ActionBuilder\Customer\CustomerActionBuilder;
use BestIt\CommercetoolsODM\ActionBuilder\Customer\SetCustomField;
use BestIt\CommercetoolsODM\Mapping\ClassMetadataInterface;
use BestIt\CommercetoolsODM\Tests\ActionBuilder\SupportTestTrait;
use Commercetools\Core\Model\Common\DateTimeDecorator;
use Commercetools\Core\Model\Customer\Customer;
use Commercetools\Core\Model\Product\Product;
use Commercetools\Core\Request\CustomField\Command\SetCustomFieldAction;
use DateTime;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Tests SetCustomField.
 * @author lange <lange@bestit-online.de>
 * @category Tests
 * @package BestIt\CommercetoolsODM
 * @subpackage ActionBuilder\ProductType
 * @version $id$
 */
class SetCustomFieldTest extends TestCase
{
    use SupportTestTrait;

    /**
     * The test class.
     * @var SetCustomField|PHPUnit_Framework_MockObject_MockObject
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
            ['custom/fields/bob', Customer::class, true],
            ['custom/bob/', Customer::class],
            ['custom/fields/bob', Product::class],
        ];
    }

    /**
     * Sets up the test.
     * @return void
     */
    public function setUp()
    {
        $this->fixture = new SetCustomField();
    }

    /**
     * Checks if a simple action is created.
     * @covers SetCustomField::createUpdateActions()
     * @return void
     */
    public function testCreateUpdateActionsDatetime()
    {
        $customer = new Customer();

        $this->fixture->setLastFoundMatch([uniqid(), $field = uniqid()]);

        $actions = $this->fixture->createUpdateActions(
            $value = new DateTime(),
            static::createMock(ClassMetadataInterface::class),
            [],
            [],
            $customer
        );

        static::assertCount(1, $actions);
        static::assertInstanceOf(SetCustomFieldAction::class, $actions[0]);
        static::assertSame($field, $actions[0]->getName());
        static::assertInstanceOf(DateTimeDecorator::class, $actions[0]->getValue());
    }

    /**
     * Checks if a simple action is created.
     * @covers SetCustomField::createUpdateActions()
     * @return void
     */
    public function testCreateUpdateActionsScalar()
    {
        $customer = new Customer();

        $this->fixture->setLastFoundMatch([uniqid(), $field = uniqid()]);

        $actions = $this->fixture->createUpdateActions(
            $value = uniqid(),
            static::createMock(ClassMetadataInterface::class),
            [],
            [],
            $customer
        );

        static::assertCount(1, $actions);
        static::assertInstanceOf(SetCustomFieldAction::class, $actions[0]);
        static::assertSame($field, $actions[0]->getName());
        static::assertSame($value, $actions[0]->getValue());
    }

    /**
     * Checks the instance.
     * @return void
     */
    public function testInstance()
    {
        static::assertInstanceOf(CustomerActionBuilder::class, $this->fixture);
    }
}
