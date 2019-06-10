# AuthService


##laravel 5.5.*
>composer 指令

    composer require alamb/auth-service
    
    
###1.先发布配置文件到config目录下面

    php artisan vendor:publish
    
###2.在app/config目录注册我们的服务提供

 `````php
    'providers'=> [
        ...
        \Alamb\AuthService\AuthProvider::class,
    ]
  `````
  
####测试用例
`````php
   public function test()
      {
          $response=app('authService')->getResponse(
              'POST', //请求类型
            'http://new.alamb.com/login',  //请求连接
            [                                  //请求数据
                'password'=>"1234567",
                'phone'=>'13435439932',]
            );
          dump($response);
      }
          
`````
    

##lumen下安装必要的包拓展包