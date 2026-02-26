type Actions = App.Message.ActionRegistry;
type Input<K extends keyof Actions> = Actions[K]['input'];
type Output<K extends keyof Actions> = Actions[K]['output'];

export function rpc<K extends keyof Actions>(action: K) {
    return async (input: Input<K>) => {
        const response = await fetch(`/api/${action}`, {
            body: JSON.stringify(input),
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'accept/application/json',
            },
        });

        const json = await response.json();

        return json as Output<K>;
    };
}
