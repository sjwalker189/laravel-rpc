# Laravel RPC

Procedures are defined as independent, namespaced modules inside `App\Message\*`

Procedures are mounted to `/api/rpc/{name}`
    - Currently using `GET` but should be `POST` in future. Could let the procedure define what methods it accepts?

Procedures, Inputs and Outputs don't need to be marked with the `#[TypeScript]` attribute -- This is handled by the special Manifest class and transformer.

Types for all procedures are generated as a union type. This can be narrowed on the client side using supporting API's. 

For example:

```php

// App\Message\Ping\PingAction.php

class PingAction {
    public function handle(string $msg): string 
    {
        return $msg;
    }
}

// App\Message\Manifest.php

#[TypeScript]
class Manifest
{
    public array $actions = [
        'ping' => PingAction::class,
        'user.create' => CreateUserAction::class,
    ];
}
```

```ts
namespace App.Message {
 export type Message = 
    | { name: "ping", input: string, output: string } 
    | { name: "user.create", input:  { name: string, email: string }, output: { id: number; name: string; email: string; activatedAt?: Date;  } } 
}

// 'ping' is type checked and narrows the type of the following params
const result = rpc('ping', 'hello'); // Promise<string>
```

TODO: Need to reflect `DataCollection` type hints to resolve more complex / generic types.


