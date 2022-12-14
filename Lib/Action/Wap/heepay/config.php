<?php
$config = array (	
		//汇付宝商户号
		'merch_id' => "2139386",

		//商户私钥
		'merchant_private_key' => "MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQC6/ydDqp5o3Qa+bLUqPO+etYn5TT5dTIkQQ/oB+quSgO/R70v9jy6jduFzSGGqTmn5N4dPfKZilDm/3VaeJh/eORDFbqig+JJETDdOIZnkEEn6YGTg8DtwMr/QmfJy+8w0Pr/IAUwbMXgUe+VCFEYRspRvGHJ2XOF1IFw7V+28aeuLWW0PkH945APaHp4L9T2iCSs9I+5bNK8LrIF2DRlx4NYqtMavBuENjDmD79dsRyTkqa4BHC/wg6BrHXLWPH4RxGI2ZjtSvKdbsubkSnLBQ6TNZN9wiqa4eRn8/7rDlODrzb6Z1Spqx4DJJ6dYINWM27Pr8N00Dt2Cl/pGjVelAgMBAAECggEANIPVng47gI2aCD51PlBwpuyqu+Wyfvcwgu3kN0wThQhK0XVXrPTaDzQiqoKIUxDEeCXdDTifbY3dDgH2AmIIjxsNl7S1DMfiI+YXngyXsFHWxMbvbbBpsN+/uLCTQzFtrrp0l5GtsvFYnMASqVUSPIQfZXfDJXR+KKuW21+dN0034ofuTlE6+gpHiraVtuqeaaJhxWKm/KBpOxJ/1YhPS1o8kdq3MjSIgsl37ovaGKjqamvkylBEMk3NolMTcDUvJh8ZoLWNWNbawXr6Jo56iBbU4N4OLkev2EOI3T3Xnwlcw1mWaKMPy2F+QyyqfqjStgPoKYFw4qwHMh/OPatnfQKBgQDAo8l0uzUMBMcrXMw5Q+ou832o22em+jAnOAP2K0HNdIsdvr+FK+q4mAKfbQyJOChh6XmYZpw2qcaLszgSUg6dUdy3FOYqHPnSKg8XltIrVBrFXEApwD+h3UwEC7U6D1zgnqy7w6jnzTwfvYIK+hxgIKDGeRmhwh2FfJ35o7X8nwKBgQD4gDgX9Kr+dYACt9mrUn3KUHOfF/8fHybcUZ/aJLHU1WhqYCUlEZ053nuyUbMEejVyVKJN58PblgV6wULPEC5jOmoOsi6Scw5vbdfHqLSzGqGFsI6Avs1f9me5sw0YYt0bX2otWqbvFYkheIslZZRi96l2RK5x3tbulskwlrCBOwKBgDHZcGU7mIOOrPeEoPhkobIaojbS5+Sms1VCwouuL+35rZI57ReKAMhZ1bvpnSfZF2IW57dPPjdLAaze6LCc+VkueN4Lk2/sZZ1D8vnYtnQt5GuT7qqfLBg3ytb1LKVkmlUp2msQO6IYUumnwYITrMoXR2N0rPRV5gvH7p1OBubjAoGAIE5K/JJKSJpt8eyE18j5oXukDGLKP/mEy8+wwGNU2x6DXJDzQ0Zu8j8CRcRpSYO5vwtRrl8bD0kJnVPSo6iu3yeQ8ign9dIPZl0ZWFOOalpj9UVmwUYM3RTjlzi30xvHMu/MlejbGunp0fgh3tK937/iwAVdyF/4ATyJG0/70lECgYEAnLAgDAjRw5GJG8NK9Zdo37j06Y59/KC+cFjB/770Lxi3x9/S0lEGw3MzVTud/1yMMgwqiOaV+jgUOe75LZPNdvzzvVEUmMgxdB7NMN4lYhRsx9QltGWxMWFz2dR8jOxl2t3Q1ew0hGtO+jHUpK0cmfXbYUEvCmYDgvty1D+0vG8=",
        //商户公钥
        'merchant_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAuv8nQ6qeaN0Gvmy1KjzvnrWJ+U0+XUyJEEP6AfqrkoDv0e9L/Y8uo3bhc0hhqk5p+TeHT3ymYpQ5v91WniYf3jkQxW6ooPiSREw3TiGZ5BBJ+mBk4PA7cDK/0JnycvvMND6/yAFMGzF4FHvlQhRGEbKUbxhydlzhdSBcO1ftvGnri1ltD5B/eOQD2h6eC/U9ogkrPSPuWzSvC6yBdg0ZceDWKrTGrwbhDYw5g+/XbEck5KmuARwv8IOgax1y1jx+EcRiNmY7UrynW7Lm5EpywUOkzWTfcIqmuHkZ/P+6w5Tg682+mdUqaseAySenWCDVjNuz6/DdNA7dgpf6Ro1XpQIDAQAB",
		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA",

		//汇付宝网关
		'gateway_Url' => "http://211.103.157.45/PayHeepay/API/PageSign/Index.aspx",//"https://test.Heepay.com/API/PageSign/Index.aspx",

		//汇付宝公钥
		'heepay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA8NHm2ZHWN/C2Frs3qFyLvXO1dzq6GuXHK0lRzKoIrZWReuJpNUIXY6ZxIk4Tj91W1JLF7D9r4bfj9FW5hS330fsrh3RqXsJpqV3KQay/lj+/n/gumFBVOdi92IXr4lDSxF8pW4ciS25xuazmW208nFvwWp5Ew11C5exjd0GdsUcBXuGhR5LWa4guyjLszli7ucWui4rK3h1zzmim0cUn2weZPq8ofk6qlbUHCaph2EfQFZhF/H+d4tzlsDVrjQg8KbMtpCULF2UgOd+jtycpSA4SlXJOAlbdyMzjXfNCfAAkPqYYFHgV97BhPC5tpYQzFVJo+b7wwZ7fFeikj2s+XwIDAQAB",

        //日志路径
        'log_path' => "log",
);
?>