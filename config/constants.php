<?php

return [
    'APP_NAME' => 'MIE TOZ',
    'FOOTER_NAME' => 'Vinny Rahmasari',
    'UPLOAD_PATH' => strpos(env('APP_URL'), 'https://') === 0 ? base_path('../public_html/upload_images') : public_path('upload_images')
];
