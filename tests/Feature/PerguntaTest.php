<?php

use App\Models\Evento;
use App\Models\Pergunta;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->evento = Evento::create(['titulo' => 'Evento A']);
});

// Ticket #001
it('rejeita pergunta vazia com 422', function () {
    $this->actingAs($this->user)
        ->postJson("/eventos/{$this->evento->id}/perguntas", ['texto' => ''])
        ->assertStatus(422)
        ->assertJsonValidationErrors('texto');
});

it('rejeita pergunta de uma letra com 422', function () {
    $this->actingAs($this->user)
        ->postJson("/eventos/{$this->evento->id}/perguntas", ['texto' => 'a'])
        ->assertStatus(422)
        ->assertJsonValidationErrors('texto');
});

it('rejeita pergunta com mais de 255 caracteres', function () {
    $this->actingAs($this->user)
        ->postJson("/eventos/{$this->evento->id}/perguntas", ['texto' => str_repeat('a', 256)])
        ->assertStatus(422)
        ->assertJsonValidationErrors('texto');
});

it('rejeita evento inexistente com 422', function () {
    $this->actingAs($this->user)
        ->postJson('/eventos/999999/perguntas', ['texto' => 'Pergunta válida de teste'])
        ->assertStatus(422)
        ->assertJsonValidationErrors('evento_id');
});

it('aceita pergunta válida', function () {
    $this->actingAs($this->user)
        ->post("/eventos/{$this->evento->id}/perguntas", ['texto' => 'Pergunta válida de teste'])
        ->assertRedirect(route('eventos.show', $this->evento->id));

    expect(Pergunta::where('evento_id', $this->evento->id)->count())->toBe(1);
});

// Ticket #002
it('pagina 10 perguntas por vez, só do evento atual e mais recentes primeiro', function () {
    $outro = Evento::create(['titulo' => 'Evento B']);

    foreach (range(1, 25) as $i) {
        Pergunta::create([
            'evento_id'  => $this->evento->id,
            'texto'      => "Pergunta número {$i} do evento A",
            'created_at' => now()->subMinutes(100 - $i),
        ]);
    }
    Pergunta::create(['evento_id' => $outro->id, 'texto' => 'Pergunta do evento B']);

    $response = $this->get("/eventos/{$this->evento->id}")->assertOk();

    $perguntas = $response->viewData('perguntas');

    expect($perguntas->perPage())->toBe(10)
        ->and($perguntas->total())->toBe(25)
        ->and($perguntas->count())->toBe(10)
        ->and($perguntas->first()->texto)->toBe('Pergunta número 25 do evento A')
        ->and($perguntas->pluck('evento_id')->unique()->all())->toBe([$this->evento->id]);

    $response->assertSee('pagination', false);
});
