# Laravel WebSocket chat server

![Version](https://poser.pugx.org/ollyxar/websockets-chat/v/stable.svg)
![Downloads](https://poser.pugx.org/ollyxar/websockets-chat/d/total.svg)
![License](https://poser.pugx.org/ollyxar/websockets-chat/license.svg)

![logo](https://i.imgur.com/EAROGwT.jpg)
<meta property="og:image" content="https://i.imgur.com/EAROGwT.jpg" />

## Requirements

* Unix (extension [pcntl_fork](http://php.net/manual/function.pcntl-fork.php))
* PHP 7.1+
* Laravel 5
* composer

## Installing WebSockets Chat

The recommended way to install WebSockets is through
[Composer](http://getcomposer.org).


```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php
```

Next, run the Composer command to install the latest stable version of WebSockets:

```bash
php composer.phar require ollyxar/websockets-chat
```

After updating composer, add the service provider to the `providers` array in `config/app.php`

```php
Ollyxar\WSChat\WSChatServiceProvider::class,
```

## Configuration

You can customize variables bellow by adding config-file: `websockets-chat.php` in the config folder:

| parameter | description | example |
| --- | --- | ---: |
| handler  | Handler Class (extends of Worker) | `\App\MyHandler` |
| host     | Host (ip)   |  `0.0.0.0` |
| port     | Port        |  `2083`    |
| worker_count | Count of forked process | `4` |
| use_ssl  | Used protocol | `false` |
| cert     | PEM certificate | `/etc/nginx/conf.d/wss.pem` |
| pass_phrase | PEM certificate pass phrase | `secret$#%` |

## Extended Handler class

This is example how to use Handler with User authentication. If you have default configuration and file-session-storage you can use this example.

First you have to install auth-helper:
 
 ```bash
 php composer.phar require ollyxar/laravel-auth
 ```
 
 Then create your `Handler.php`:
 
 ```php
 namespace App;
 
 use Ollyxar\LaravelAuth\FileAuth;
 use Ollyxar\WebSockets\Frame;
 use Ollyxar\WebSockets\Worker;
 
 /**
  * Class Handler
  * @package App
  */
 class Handler extends Worker
 {
     /**
      * Connected users
      *
      * @var array
      */
     protected $users = [];
 
     private function fillUser(array $headers, $socket): bool
     {
         if ($userId = FileAuth::getUserIdByHeaders($headers)) {
             // allow only one connection for worker per user
             if (!in_array($userId, $this->users)) {
                 $this->users[(int)$socket] = $userId;
                 return true;
             }
         }
 
         return false;
     }
 
     /**
      * @param $client
      */
     protected function onConnect($client): void
     {
         $userName = User::where('id', (int)$this->users[(int)$client])->first()->name;
 
         $this->sendToAll(Frame::encode(json_encode([
             'type'    => 'system',
             'message' => $userName . ' connected.'
         ])));
     }
 
     /**
      * @param array $headers
      * @param $socket
      * @return bool
      */
     protected function afterHandshake(array $headers, $socket): bool
     {
         return $this->fillUser($headers, $socket);
     }
 
     /**
      * @param $clientNumber
      */
     protected function onClose($clientNumber): void
     {
         $userName = User::where('id', (int)$this->users[$clientNumber])->first()->name;
 
         $this->sendToAll(Frame::encode(json_encode([
             'type'    => 'system',
             'message' => $userName . " disconnected."
         ])));
         
         unset($this->users[$clientNumber]);
     }
 
     /**
      * @param string $message
      * @param int $socketId
      */
     protected function onDirectMessage(string $message, int $socketId): void
     {
         $message = json_decode($message);
         $userName = User::where('id', (int)$this->users[$socketId])->first()->name;
         $userMessage = $message->message;
 
         $response = Frame::encode(json_encode([
             'type'    => 'usermsg',
             'name'    => $userName,
             'message' => $userMessage
         ]));
 
         $this->sendToAll($response);
     }
 }
 ```
 
Then add markup to the front:
 
```html
<div class="chat-wrapper">
    <div class="message-box" id="message-box"></div>
    <div class="panel">
        <input type="text" name="message" id="message" placeholder="Message"/>
        <button id="send-btn" class="button">Send</button>
    </div>
</div>
```

And JS code:

```javascript
var wsUri = "ws://laravel5.dev:2083",
    ws = new WebSocket(wsUri);

ws.onopen = function () {
    var el = document.createElement('div');
    el.classList.add('system-msg');
    el.innerText = 'Connection established';
    document.getElementById('message-box').appendChild(el);
};

document.getElementById('message').addEventListener('keydown', function (e) {
    if (e.keyCode === 13) {
        document.getElementById('send-btn').click();
    }
});

document.getElementById('send-btn').addEventListener('click', function () {
    var mymessage = document.getElementById('message').value;

    if (mymessage === '') {
        alert("Enter Some message Please!");
        return;
    }

    var objDiv = document.getElementById("message-box");
    objDiv.scrollTop = objDiv.scrollHeight;

    var msg = {
        message: mymessage
    };
    ws.send(JSON.stringify(msg));
});

ws.onmessage = function (ev) {
    var msg = JSON.parse(ev.data),
        type = msg.type,
        umsg = msg.message,
        uname = msg.name;

    var el = document.createElement('div');

    if (type === 'usermsg') {
        el.innerHTML = '<span class="user-name">' + uname + '</span> : <span class="user-message">' + umsg + '</span>';
        document.getElementById('message-box').appendChild(el);
    }
    if (type === 'system') {
        el.classList.add('system-msg');
        el.innerText = umsg;
        document.getElementById('message-box').appendChild(el);
    }

    document.getElementById('message').value = '';

    var objDiv = document.getElementById('message-box');
    objDiv.scrollTop = objDiv.scrollHeight;
};

ws.onerror = function (e) {
    var el = document.createElement('div');
    el.classList.add('system-error');
    el.innerText = 'Error Occurred - ' + e.data;
    document.getElementById('message-box').appendChild(el);
};
ws.onclose = function () {
    var el = document.createElement('div');
    el.classList.add('system-msg');
    el.innerText = 'Connection Closed';
    document.getElementById('message-box').appendChild(el);
};
```

### Starting WebSocket Server

```bash
php artisan websockets-chat:run
```

### Sending direct message to the server

```bash
php artisan websockets-chat:send "Hello from system!"
```