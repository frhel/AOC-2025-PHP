<?php
use parallel\Runtime;
use parallel\Channel;

$totalItems = 400;
$threads    = 4;
$chunkSize  = (int) ceil($totalItems / $threads);

// Create a communication line
$channel = new Channel();

// Store futures just to keep parallel happy
$futures = []; 

for ($i = 0; $i < $threads; $i++) {
    $runtimes[$i] = new Runtime();
    
    $start = ($i * $chunkSize) + 1;
    $end   = ($i + 1) * $chunkSize;

    // CHANGE: Assign the result to $futures[]
    // We don't actually use this variable, but it silences the error.
    $futures[] = $runtimes[$i]->run(function($start, $end, $channel) {
        
        for ($id = $start; $id <= $end; $id++) {
            usleep(5000); 

            if ($id === 325) {
                $channel->send($id);
                return; // This return was causing the error because it wasn't caught
            }
        }
        
        $channel->send(false);

    }, [$start, $end, $channel]);
}

// 2. The Listener Loop
// We wait for messages. We only need to loop as many times as we have threads.
$activeThreads = $threads;

while ($activeThreads > 0) {
    // recv() blocks execution until a message arrives
    $result = $channel->recv();

    if ($result !== false) {
        echo "✅ Found it! Item ID: $result\n";
        echo "⚡ Stopping all other threads immediately.\n";
        
        // This is the "Emergency Stop" button
        // It kills all running threads so we don't waste CPU
        foreach ($runtimes as $rt) {
            $rt->kill();
        }
        break;
    }

    // If we received 'false', it means one thread finished without finding it.
    $activeThreads--;
}

if ($activeThreads === 0) {
    echo "Item not found in any thread.\n";
}