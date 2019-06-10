<?php

return[
    'service_config'=>[
        'service_id'=>"1",
        'app_key'=>"courseService",
        'public_key'=>"MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDQ3oVXutmm/XCwMT9QeTvvvMWXxVYCYUT2BNKX4wNpl6Ji+Rs2EK0C9MMJcm3b95lVJpXzKxIQZSC6rxPoTUxvD3tW/SKidT4Rk/VBnhWlGwU+wO8pHWv/a5wjByitU3KPR3P15DZBWxSurzU+RoPEIkanHeFDCyJQhYzV5ofgqQIDAQAB",
        'private_key'=>"MIICeAIBADANBgkqhkiG9w0BAQEFAASCAmIwggJeAgEAAoGBANDehVe62ab9cLAxP1B5O++8xZfFVgJhRPYE0pfjA2mXomL5GzYQrQL0wwlybdv3mVUmlfMrEhBlILqvE+hNTG8Pe1b9IqJ1PhGT9UGeFaUbBT7A7ykda/9rnCMHKK1Tco9Hc/XkNkFbFK6vNT5Gg8QiRqcd4UMLIlCFjNXmh+CpAgMBAAECgYEAp72NKN+OXGW9UkK6rk4urGHV7gU1BcRINau51PEjlHGKoCGekNTjnTQkTjnvsHAwXAoY8qjsYO5WUpa4Th+F+VeRz0zcYGz84Hoh6B/YpoTSGvW0hMMcxFA4hrZ/cgI67xyFqSMQB/cYZaB6SoLXKcmS37ZXYARdcwnle1mWwAECQQDx+w0y019Qpf2mrp7aBjIGPHMZmC7TXW/HRYWf/1l77yvjwca/s87AR03U4R/kG9OcqBMan3hudrB7s6TgjcepAkEA3PhgDeGfUW715FCMHFjiQ6GI4dOZjn6/DRCoJbiPGKCauXkcseixX52mTlMhoMX3cdRjBFnYy+vpAEYKd2TxAQJALrBW4pigCmqMn22P3tdVLZjaSHTxi/y6RYOnfbCCLoR+Pmq0E2b5HGZloQ5y3ct0sARJ81gTn5StBqEpzd3tSQJBAIJdwwXNjBoWNdoar172DZ/LCJ/7IAhSvNKvdhSzGzFzZ3Vvf79ywlrf/sMNBrT8tu0gWb6yZ1Z/+zpfGEOyeQECQQDl7mMqaK1aP7N7pwucpHHGaNH3NMtL2ZpXxs4C1wgrIckwRciKFocmz1GQnjXmbIjTos8sdFBuHecvuUekCAVa",
        'mode'=>'dev',//开发测试模式
        'url'=>'http://new.alamb.com',//连接地址
        ],
    'api_list'=>[
        'login'=>['POST','/login'],
        'register'=>['POST','/register'],
        'resetPassword'=>['POST','/resetPassword'],
        'sendMessage'=>['POST','/sendMessage'],
        'checkPhoneCode'=>['POST','/checkPhoneCode'],

        'ticket'=>['GET','/ticket'],
        'refreshTicket'=>['GET','/refreshTicket'],
        'createTicket'=>['POST','/ticket'],

        'makeSign'=>['GET','/makeSign'],

        //权限
        'permissions'=>['GET','/permissions'],
        'createPermissions'=>['POST','/permissions'],
    ]
];