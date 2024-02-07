<?php

namespace Alura\Leilao\Tests\Integration\Dao ;

use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Model\Leilao;
use PHPUnit\Framework\TestCase;

class LeilaoDaoTest extends TestCase
{
     /**@var \PDO */
    private static $pdo;
  public static function setUpBeforeClass():void
  {
    self::$pdo = new \PDO('sqlite::memory:');
    self::$pdo->exec('create table leiloes (
      id INTEGER primary key,
      descricao TEXT,
      finalizado BOOL,
      dataInicio TEXT
  );');
  }
  protected function setUp():void
  {
      self::$pdo->beginTransaction();
  }

     /**
       * @dataProvider leiloes
       */
    public function testBuscaLeiloesNaoFinalizados(array  $leiloes)
    {
       $leilaoDao = new LeilaoDao( self::$pdo );

          foreach($leiloes as $leilao){
            $leilaoDao->salva($leilao);
          }

        $leiloes = $leilaoDao->recuperarNaoFinalizados();
    
        self::assertCount(1,$leiloes);
        self::assertContainsOnlyInstancesOf(Leilao::class, $leiloes);
        self::assertSame(
          'Variante 0km',
          $leiloes[0]->recuperarDescricao()
        );
        
      }

      /**
       * @dataProvider leiloes
       */
      public function testBuscaLeiloesFinalizados(array $leiloes)
      {
          $leilaoDao = new LeilaoDao( self::$pdo );

          foreach($leiloes as $leilao){
            $leilaoDao->salva($leilao);
          }
          $leiloes = $leilaoDao->recuperarFinalizados();
  
          self::assertCount(1,$leiloes);
          self::assertContainsOnlyInstancesOf(Leilao::class, $leiloes);
          self::assertSame(
            'Fiat 147 0km',
            $leiloes[0]->recuperarDescricao()
          );
          
        }

      //       /**
      //  * @dataProvider leiloes
      //  */
      // public function testAoAtualizarLeilaoStatusDeveSerAlterado()
      // {
      //     //arrange
      //     $leilao = new Leilao('Brasilia Amarela');
      //     $leilaoDao = new LeilaoDao( self::$pdo );
      //     $leilaoDao->salva($leilao);

      //     $leiloes = $leilaoDao->recuperarNaoFinalizados();
        
      //     //Não necessário, é um teste a mais que deve ser analizado - asserts intermediários
      //     self::assertCount(1,$leiloes);
      //     self::assertContainsOnlyInstancesOf(Leilao::class, $leiloes);
      //     self::assertSame(
      //       'Brasilia Amarela',
      //       $leiloes[0]->recuperarDescricao()
      //     );
      //     self::assertFalse($leiloes[0]->estaFinalizado());

      //     $leilao->finaliza();
  
      //     //act
      //     $leilaoDao->atualiza($leilao);

      //     //assert
      //     $leiloes = $leilaoDao->recuperarFinalizados();
  
      //     self::assertCount(1,$leiloes);
      //     self::assertContainsOnlyInstancesOf(Leilao::class, $leiloes);
      //     self::assertSame(
      //       'Brasilia Amarela',
      //       $leiloes[0]->recuperarDescricao()
      //     );
      //     self::assertTrue($leiloes[0]->estaFinalizado());
          
      //   }
      protected function tearDown(): void
      {

        // self::$pdo->exec('DELETE FROM leiloes;');
        self::$pdo->rollBack();
      }
      public function leiloes()
      {
        $naoFinalizado = new Leilao('Variante 0km');
        $finalizado = new Leilao('Fiat 147 0km');
        $finalizado->finaliza();

        return [
            [
              [$naoFinalizado, $finalizado]
            ]
            ];
      }

}
