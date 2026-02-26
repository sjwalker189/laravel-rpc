declare namespace App.Message {
    export type ActionRegistry = {
        ping: App.Message.Ping.PingAction;
    };
}
declare namespace App.Message.Ping {
    export type PingAction = { input: PingInput; output: PingInput };
    export type PingInput = {
        message: string;
    };
}
