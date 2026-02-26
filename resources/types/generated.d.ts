declare namespace App.Message {
export type Message = 
  | { name: "ping", input: { message: string }, output: { message: string } };
}
