<?php

namespace JWTAuth\Helpers;

use Illuminate\Database\Eloquent\Model;

class JWTHelpers {
    /**
     * getPayloadDataFields
     *
     * @param Model $user
     *
     * @return array
     */
    public static function getPayloadDataFields(Model $user): array
    {
        $subField = config('jwt.sub_payload_field');
        $nameFields = config('jwt.name_payload_fields');

        $subValue = '';
        if (!empty($subField)) {
            $subValue = $user->{$subField};
        }

        $nameValue = '';
        foreach ($nameFields as $field) {
            $nameValue .= $user->{$field} . ' ';
        }
        $nameValue = trim($nameValue);

        return [
            $subValue,
            $nameValue,
        ];
    }
}
