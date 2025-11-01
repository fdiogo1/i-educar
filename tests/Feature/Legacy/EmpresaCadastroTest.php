<?php
<?php

namespace Tests\Feature\Legacy;

use Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class EmpresaCadastroTest extends TestCase
{
    private $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = new \clsEmpresa();
    }

    /**
     * Cobre: CT01 - CNPJ preenchido, mas inválido.
     * MC/DC: D1 (linha 1)
     */
    public function testNovoComCnpjInvalido()
    {
        // NOTA: Assumindo que a função global validaCNPJ() retornará 'false'
        // para um CNPJ de formato obviamente inválido.
        $this->sut->cnpj = '111'; // D1C1 = V

        // A função 'validaCNPJ' deve retornar 'false' (D1C2 = V)
        $resultado = $this->sut->Novo(); 

        $this->assertFalse($resultado);
        $this->assertEquals('CNPJ inválido', $this->sut->mensagem);
    }

    /**
     * Cobre: CT02 - CNPJ válido, mas já cadastrado.
     * MC/DC: D1 (linha 2), D2 (linha 1)
     */
    public function testNovoComCnpjDuplicado()
    {
        // NOTA: Teste complexo. Depende de 'new clsJuridica' e 'validaCNPJ'.
        // Exigiria refatoração para injeção de dependência ou mock de 'new'.
        $this->markTestIncomplete('Depende de mock para "new clsJuridica" e "validaCNPJ"');
    }

    /**
     * Cobre: CT03 - Caminho feliz com CNPJ vazio.
     * MC/DC: D1 (linha 3), D2 (linha 3)
     */
    public function testNovoCaminhoFelizComCnpjVazio()
    {
        // Criamos um mock parcial para simular os métodos internos
        $this->sut = $this->createPartialMock(get_class($this->sut), [
            'validaCaracteresPermitidosComplemento',
            'validaDadosTelefones',
            'simpleRedirect' // Mockar para evitar erro de 'headers already sent'
        ]);

        // Configuração
        $this->sut->cnpj = ''; // D1C1=F, D2C1=F
        $this->sut->method('validaCaracteresPermitidosComplemento')->willReturn(true); // D3C1=F
        $this->sut->method('validaDadosTelefones')->willReturn(true); // D4C1=F

        // Execução
        $resultado = $this->sut->Novo();

        // Verificação
        $this->assertTrue($resultado);
        $this->assertEquals('Cadastro salvo com sucesso.', $this->sut->mensagem);
    }

    /**
     * Cobre: CT04 - Caminho feliz com CNPJ válido.
     * MC/DC: D2 (linha 2)
     */
    public function testNovoCaminhoFelizComCnpjValido()
    {
        // NOTA: Teste complexo, similar ao CT02.
        $this->markTestIncomplete('Depende de mock para "new clsJuridica" e "validaCNPJ"');
    }

    /**
     * Cobre: CT05 - Complemento inválido.
     * MC/DC: D3 (V)
     */
    public function testNovoComComplementoInvalido()
    {
        $this->sut = $this->createPartialMock(get_class($this->sut), [
            'validaCaracteresPermitidosComplemento'
        ]);

        // Configuração
        $this->sut->cnpj = ''; // D1C1=F, D2C1=F (caminho mais simples para chegar em D3)
        $this->sut->method('validaCaracteresPermitidosComplemento')->willReturn(false); // D3C1=V

        // Execução
        $resultado = $this->sut->Novo();

        // Verificação
        $this->assertFalse($resultado);
        $this->assertStringContainsString('O campo foi preenchido com valor não permitido.', $this->sut->mensagem);
    }

    /**
     * Cobre: CT06 - Telefone inválido.
     * MC/DC: D4 (V)
     */
    public function testNovoComTelefoneInvalido()
    {
        $this->sut = $this->createPartialMock(get_class($this->sut), [
            'validaCaracteresPermitidosComplemento',
            'validaDadosTelefones'
        ]);

        // Configuração
        $this->sut->cnpj = ''; // D1C1=F, D2C1=F
        $this->sut->method('validaCaracteresPermitidosComplemento')->willReturn(true); // D3C1=F
        $this->sut->method('validaDadosTelefones')->willReturn(false); // D4C1=V

        // Execução
        $resultado = $this->sut->Novo();

        // Verificação
        $this->assertFalse($resultado);
        $this->assertTrue($this->sut->busca_empresa);
    }
}