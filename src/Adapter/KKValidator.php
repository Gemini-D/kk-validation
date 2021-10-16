<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Hyperf\Validation\Adapter;

use Hyperf\Contract\TranslatorInterface;
use Hyperf\Contract\ValidatorInterface;
use Hyperf\Utils\Contracts\MessageBag as MessageBagContract;
use Hyperf\Utils\MessageBag;
use KK\Validation\Validator;
use Psr\Container\ContainerInterface;

class KKValidator implements ValidatorInterface
{
    /**
     * The array of custom error messages.
     *
     * @var array
     */
    public $customMessages = [];

    /**
     * The array of custom attribute names.
     *
     * @var array
     */
    public $customAttributes = [];

    /**
     * The data under validation.
     *
     * @var array
     */
    protected $data;

    /**
     * All of the registered "after" callbacks.
     *
     * @var array
     */
    protected $after = [];

    /**
     * The Translator implementation.
     *
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * The container instance.
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * The message bag instance.
     *
     * @var MessageBagContract
     */
    protected $messages;

    /**
     * @var Validator
     */
    protected $validator;

    public function __construct(
        TranslatorInterface $translator,
        array $data,
        array $rules,
        array $messages = [],
        array $customAttributes = []
    ) {
        $this->translator = $translator;
        $this->customMessages = $messages;
        $this->data = $data;
        $this->customAttributes = $customAttributes;

        $this->validator = new Validator($rules);
    }

    /**
     * Determine if the data passes the validation rules.
     */
    public function passes(): bool
    {
        $this->messages = new MessageBag();

        $this->validator->valid($this->data);

        [$this->distinctValues, $this->failedRules] = [[], []];

        // We'll spin through each rule, validating the attributes attached to that
        // rule. Any error messages will be added to the containers with each of
        // the other error messages, returning true if we don't have messages.
        foreach ($this->rules as $attribute => $rules) {
            $attribute = str_replace('\.', '->', $attribute);

            foreach ($rules as $rule) {
                $this->validateAttribute($attribute, $rule);

                if ($this->shouldStopValidating($attribute)) {
                    break;
                }
            }
        }

        // Here we will spin through all of the "after" hooks on this validator and
        // fire them off. This gives the callbacks a chance to perform all kinds
        // of other validation that needs to get wrapped up in this operation.
        foreach ($this->after as $after) {
            call_user_func($after);
        }

        return $this->messages->isEmpty();
    }

    public function getMessageBag(): MessageBagContract
    {
        // TODO: Implement getMessageBag() method.
    }

    public function validate(): array
    {
        // TODO: Implement validate() method.
    }

    public function validated(): array
    {
        // TODO: Implement validated() method.
    }

    public function fails(): bool
    {
        // TODO: Implement fails() method.
    }

    public function failed(): array
    {
        // TODO: Implement failed() method.
    }

    public function sometimes($attribute, $rules, callable $callback)
    {
        // TODO: Implement sometimes() method.
    }

    public function after($callback)
    {
        $this->after[] = function () use ($callback) {
            return call_user_func_array($callback, [$this]);
        };

        return $this;
    }

    public function errors(): MessageBagContract
    {
        // TODO: Implement errors() method.
    }
}
