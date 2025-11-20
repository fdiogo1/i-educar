<?php

use PHPUnit\Framework\TestCase;

class TurmaTest extends TestCase
{
    protected function setUp(): void
    {
        require_once __DIR__ . '/../../intranet/include/pmieducar/clsPmieducarTurma.inc.php';
    }

    /**
     * Ciclo 1: Verifica se hora final é menor que inicial
     */
    public function testCadastraRejeitaHoraFinalMenorQueInicial()
    {
        $turma = new clsPmieducarTurma();
        $turma->hora_inicial = '14:00';
        $turma->hora_final = '10:00'; 

        $this->assertFalse($turma->cadastra(), 'Deve falhar se fim < início');
        $this->assertEquals('A hora final deve ser maior que a inicial.', $turma->mensagem);
    }

    /**
     * Ciclo 2: Verifica se hora final é igual à inicial (duração zero)
     */
    public function testCadastraRejeitaHorariosIguais()
    {
        $turma = new clsPmieducarTurma();
        $turma->hora_inicial = '13:00';
        $turma->hora_final = '13:00'; 

        $this->assertFalse($turma->cadastra(), 'Deve falhar se fim == início');
        $this->assertEquals('A hora final deve ser maior que a inicial.', $turma->mensagem);
    }

    /**
     * Ciclo 3: Verifica se o intervalo está fora do horário da aula
     */
    public function testCadastraRejeitaIntervaloForaDoHorarioDeAula()
    {
        $turma = new clsPmieducarTurma();
        $turma->hora_inicial = '13:00';
        $turma->hora_final = '17:00';
        
        // Intervalo começa após o fim da aula
        $turma->hora_inicio_intervalo = '18:00';
        $turma->hora_fim_intervalo = '18:15';

        $this->assertFalse($turma->cadastra(), 'Deve falhar se intervalo estiver fora do turno');
        $this->assertEquals('O intervalo deve estar dentro do horário da turma.', $turma->mensagem);
    }

    /**
     * Ciclo 4: Verifica consistência interna do intervalo (Fim <= Início)
     */
    public function testCadastraRejeitaIntervaloComFimMenorQueInicio()
    {
        $turma = new clsPmieducarTurma();
        $turma->hora_inicial = '13:00';
        $turma->hora_final = '17:00';
        
        // Intervalo termina antes de começar
        $turma->hora_inicio_intervalo = '15:20';
        $turma->hora_fim_intervalo = '15:00';

        $this->assertFalse($turma->cadastra(), 'Deve falhar se fim do intervalo < início do intervalo');
        $this->assertEquals('A hora final do intervalo deve ser maior que a inicial.', $turma->mensagem);
    }
}