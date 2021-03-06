<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Test\Services\PaymentMethodNameToGatewayConverterInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Payment\Model\PaymentMethod;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class PaymentContextSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $paymentMethodRepository,
        SharedStorageInterface $sharedStorage,
        FactoryInterface $paymentMethodFactory,
        PaymentMethodNameToGatewayConverterInterface $paymentMethodNameToGatewayConverter
    ) {
        $this->beConstructedWith($paymentMethodRepository, $sharedStorage, $paymentMethodFactory, $paymentMethodNameToGatewayConverter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Setup\PaymentContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_converts_payment_method_name_into_payment_method_object(
        RepositoryInterface $paymentMethodRepository,
        PaymentMethod $paymentMethod
    ) {
        $paymentMethodRepository->findOneBy(['name' => 'Offline'])->willReturn($paymentMethod);

        $this->getPaymentMethodByName('Offline')->shouldReturn($paymentMethod);
    }

    function it_throws_element_not_found_exception_if_payment_method_has_not_been_found (
        RepositoryInterface $paymentMethodRepository
    ) {
        $paymentMethodRepository->findOneBy(['name' => 'Free'])->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('getPaymentMethodByName', ['Free']);
    }
}
