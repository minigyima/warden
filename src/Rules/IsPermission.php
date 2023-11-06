<?php

namespace Minigyima\Warden\Rules;

use Illuminate\Contracts\Validation\Rule;
use Minigyima\Warden\Facades\Warden;

class IsPermission implements Rule
{
    private string $exceptionMessage = '';

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try {
            $arr = $value;
            if (!is_array($arr)) {
                $arr = [$arr];
            }

            Warden::validatePermissions($arr);
        } catch (\Exception $exception) {
            $this->exceptionMessage = $exception->getMessage();
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->exceptionMessage;
    }
}
