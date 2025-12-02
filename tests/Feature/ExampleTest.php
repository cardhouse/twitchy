<?php

test('returns a successful response', function () {
    $response = $this->get('/control');

    $response->assertStatus(200);
});
