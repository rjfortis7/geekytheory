<?php

namespace App;

class Subscription extends \Laravel\Cashier\Subscription
{
    const PLAN_MONTHLY = 'monthly';
    const PLAN_MONTHLY_PRICE_EUR = 15;
    const PLAN_MONTHLY_NAME = 'Monthly subscription';

    const PLAN_YEARLY = 'yearly';
    const PLAN_YEARLY_PRICE_EUR = 150;
    const PLAN_YEARLY_NAME = 'Yearly subscription';
}
