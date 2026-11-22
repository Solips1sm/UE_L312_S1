<?php declare(strict_types=1);

// replay the first version with room for one callback
class EarlyDefer {
    public function __construct(private mixed $callback = null) {
    }

    public function defer(callable $callback): void {
        $this->callback = $callback;
    }

    public function __destruct() {
        if ($this->callback === null) {
            echo 'destructor';
        } else {
            call_user_func($this->callback);
        }
    }
}

$early = new EarlyDefer();
echo '1 before unset -> ';
unset($early);
echo PHP_EOL;

// the same closure records when php actually calls it
$ran = false;
$mark = function () use (&$ran): void {
    $ran = true;
};
$early = new EarlyDefer($mark);
echo '2 stored at construction, ran=', $ran ? 'yes' : 'no', PHP_EOL;
unset($early);
echo '3 after unset, ran=', $ran ? 'yes' : 'no', PHP_EOL;

$early = new EarlyDefer();
$early->defer(function (): void { echo 'registered call'; });
echo '4 constructor -> defer() -> unset() -> ';
unset($early);
echo PHP_EOL;

$galanginho = new Defer();
$galanginho('printf', '%s', 'first');
$galanginho('printf', '%s', 'last -> ');
echo '5 stack: ';
unset($galanginho);
echo PHP_EOL;

$galanginho = Defer::init();
echo '6 init() returns ', get_class($galanginho), PHP_EOL;
echo '7 object(...) -> ';
$galanginho('printf', '%s', 'call through __invoke');
unset($galanginho);
echo PHP_EOL;

$galanginho = new Defer();
$galanginho('printf', '%s=%d', 'value', 7);
echo '8 arguments -> ';
unset($galanginho);
echo PHP_EOL;

Defer::init($galanginho);
$copy = $galanginho;
$galanginho('printf', '%s', 'callback');
unset($galanginho);
echo 'bonus: after first unset, silent | after last unset -> ';
unset($copy);
echo PHP_EOL;
