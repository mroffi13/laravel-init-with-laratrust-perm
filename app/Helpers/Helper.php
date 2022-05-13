<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Session;

class Helper
{
    public static function sessionAlert($message, $class, $icon)
    {
        Session::flash('alert_message', $message);
        Session::flash('alert_class', $class);
        Session::flash('alert_icon', $icon);
    }

    public static function badge($text, $class)
    {
        return '<span class="badge badge-' . $class . ' rounded-0">' . $text . '</span>';
    }

    // public static function uploadFile($file, $caption)
    // {
    //     $media = null;
    //     $pathname = $file->getPathname();
    //     $filename = $file->getClientOriginalName();
    //     $file = fopen($pathname, 'r');

    //     $param = [
    //         'endpoint' => 'assets/store',
    //         'form_request' => [
    //             'multipart' => [
    //                 [
    //                     'name'      => 'file',
    //                     'filename'  => $filename,
    //                     'contents'  => $file
    //                 ],
    //                 [
    //                     'name'      => 'caption',
    //                     'contents'  => $caption
    //                 ]
    //             ]
    //         ],
    //     ];

    //     $response = API::post($param);
    //     $response = json_decode($response);

    //     if (!empty($response->data->media))
    //         $media = $response->data->media;

    //     return $media;
    // }

    public static function maybe_unserialize($original, $array = true)
    {
        // if ( self::is_serialized( $original ) ) // don't attempt to maybe_unserialize data that wasn't maybe_serialized going in
        if (@json_decode($original))
            return @json_decode($original, $array);

        return $original;
    }

    public static function maybe_serialize($data)
    {
        if (is_array($data) || is_object($data))
            // return maybe_serialize( $data );
            return json_encode($data);

        // Double serialization is required for backward compatibility.
        // See https://core.trac.wordpress.org/ticket/12930
        // Also the world will end. See WP 3.6.1.
        // if ( self::is_serialized( $data, false ) )
        //     return maybe_serialize( $data );

        return $data;
    }

    public static function convertMetas($attr_metas)
    {
        # code...
        $metas = [];
        foreach ($attr_metas as $user_meta)
            $metas[$user_meta->meta_key] = self::maybe_unserialize($user_meta->meta_value);

        return $metas;
    }

    public static function setCookie($key, $value)
    {
        Cookie::queue(Cookie::make($key, $value, (10 * 365 * 24 * 60 * 60)));
        Cookie::queue($key, $value, (10 * 365 * 24 * 60 * 60));
    }

    public static function getCookie($key)
    {
        $value = Cookie::get($key);
        return $value;
    }

    public static function button_defaut($data, $permission)
    {
        return [
            [
                'icon' => 'fas fa-eye', // icon action
                'label' => 'Detail', // label action
                'show' => true, // show action
                'classes' => '', // add classes
                'url' => $data->url . $data->id, // url
                'can' => 'read-' . $permission, // permission
                'attributes' => null // data-*
            ],
            [
                'icon' => 'fas fa-pen',
                'label' => 'Edit',
                'show' => true,
                'classes' => '',
                'url' => $data->url . $data->id . '/edit',
                'can' => 'update-' . $permission,
                'attributes' => null
            ],
            [
                'icon' => 'fas fa-trash',
                'label' => 'Delete',
                'show' => true,
                'url' => null,
                'classes' => 'delete-btn',
                'can' => 'delete-' . $permission,
                'attributes' => [
                    'id' => encrypt($data->id)
                ]
            ],
        ];
    }

    public static function insert_log_user($model, $login, $update = 0)
    {
        if (empty($update)) {
            $model->created_id = $login->id;
            $model->created_name = $login->name;
        }

        $model->updated_id = $login->id;
        $model->updated_name = $login->name;
    }
}
