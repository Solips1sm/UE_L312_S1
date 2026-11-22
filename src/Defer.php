<?php declare(strict_types=1);

class Callback {
    public function __construct(private mixed $callback, private array $args = []) {
        if (!is_callable($this->callback)) {
            throw new InvalidArgumentException('invalid callback');
        }
    }

    public function call(): void {
        call_user_func_array($this->callback, $this->args);
    }
}

class Defer {
    private array $callbacks = [];

    public static function init(&$defer = null): self {
        // also fill the caller's variable when one was supplied
        $defer = new self();

        return $defer;
    }

    public function __invoke(callable $callback, mixed ...$args): void {
        // store the call and its arguments together for later
        $this->callbacks[] = new Callback($callback, $args);
    }

    public function __destruct() {
        $failure = null;
        // the last registered callback runs first
        while ($this->callbacks !== []) {
            $callback = array_pop($this->callbacks);
            try {
                $callback->call();
            } catch (Throwable $error) {
                // finish the remaining callbacks before reporting the first error
                $failure = $failure ?? $error;
            }
        }

        if ($failure !== null) {
            throw $failure;
        }
    }
}
