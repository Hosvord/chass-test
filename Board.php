<?php

class Board {
	private $figures = [];
	private $turn = 'white'; // Текущий ход (белые начинают)

    public function __construct() {
        $this->figures['a'][1] = new Rook(false);
        $this->figures['b'][1] = new Knight(false);
        $this->figures['c'][1] = new Bishop(false);
        $this->figures['d'][1] = new Queen(false);
        $this->figures['e'][1] = new King(false);
        $this->figures['f'][1] = new Bishop(false);
        $this->figures['g'][1] = new Knight(false);
        $this->figures['h'][1] = new Rook(false);

        $this->figures['a'][2] = new Pawn(false);
        $this->figures['b'][2] = new Pawn(false);
        $this->figures['c'][2] = new Pawn(false);
        $this->figures['d'][2] = new Pawn(false);
        $this->figures['e'][2] = new Pawn(false);
        $this->figures['f'][2] = new Pawn(false);
        $this->figures['g'][2] = new Pawn(false);
        $this->figures['h'][2] = new Pawn(false);

        $this->figures['a'][7] = new Pawn(true);
        $this->figures['b'][7] = new Pawn(true);
        $this->figures['c'][7] = new Pawn(true);
        $this->figures['d'][7] = new Pawn(true);
        $this->figures['e'][7] = new Pawn(true);
        $this->figures['f'][7] = new Pawn(true);
        $this->figures['g'][7] = new Pawn(true);
        $this->figures['h'][7] = new Pawn(true);

        $this->figures['a'][8] = new Rook(true);
        $this->figures['b'][8] = new Knight(true);
        $this->figures['c'][8] = new Bishop(true);
        $this->figures['d'][8] = new Queen(true);
        $this->figures['e'][8] = new King(true);
        $this->figures['f'][8] = new Bishop(true);
        $this->figures['g'][8] = new Knight(true);
        $this->figures['h'][8] = new Rook(true);
    }
    public function move($move) {
        if (!preg_match('/^([a-h])(\d)-([a-h])(\d)$/', $move, $match)) {
            throw new \Exception("Incorrect move");
        }
//		print_r($match);
        $xFrom = $match[1];
        $yFrom = $match[2];
        $xTo   = $match[3];
        $yTo   = $match[4];

	    // Проверка на наличие фигуры на начальной позиции
	    if (!isset($this->figures[$xFrom][$yFrom])) {
		    throw new \Exception("No piece at starting position");
	    }

	    // Определение цвета фигуры на начальной позиции
	    $movingFigure = $this->figures[$xFrom][$yFrom];
	    $color = $movingFigure->isBlack() ? 'black' : 'white'; // Проверка цвета фигуры

	    // Проверка очередности хода
	    if ($color !== $this->turn) {
		    throw new \Exception("It's not {$color}'s turn");
	    }
	    // Переключение хода
	    $this->turn = ($this->turn === 'white') ? 'black' : 'white';
//		var_dump($this->turn);

	    // Перемещение Пешки
	    if ($movingFigure instanceof Pawn) {
		    // Определяем направление движения для пешки
		    $direction = $color === 'black' ? -1 : 1; // Черные движутся вниз (от 8 к 1), белые вверх (от 1 к 8)

		    // Ход на одну клетку вперед
		    if ($xFrom === $xTo && (int)$yTo === (int)$yFrom + $direction) {

			    // Пустая клетка впереди
			    if (!isset($this->figures[$xTo][$yTo])) {
				    $this->figures[$xTo][$yTo] = $movingFigure;
				    unset($this->figures[$xFrom][$yFrom]);
				    return;
			    }
		    }

		    // Ход на две клетки вперед (только с начальной позиции)
		    if ($xFrom === $xTo && (int)$yTo === (int)$yFrom + 2 * $direction && ((int)$yFrom === 2 || (int)$yFrom === 7)) {
			    // Проверка, что две клетки впереди пусты
			    if (!isset($this->figures[$xTo][$yTo]) && !isset($this->figures[$xTo][(int)$yFrom + $direction])) {
				    $this->figures[$xTo][$yTo] = $movingFigure;
				    unset($this->figures[$xFrom][$yFrom]);
				    return;
			    }
		    }


		    // Атакующий ход
		    $xFromIndex = ord($xFrom) - ord('a');
		    $xToIndex = ord($xTo) - ord('a');
		    if (abs($xFromIndex - $xToIndex) === 1 && (int)$yTo === (int)$yFrom + $direction) {
			    // Проверка, что на клетке, которую атакует пешка, есть фигура противника
			    if (isset($this->figures[$xTo][$yTo]) && $this->figures[$xTo][$yTo]->isBlack() !== $color) {
				    $this->figures[$xTo][$yTo] = $movingFigure;
				    unset($this->figures[$xFrom][$yFrom]);
				    return;
			    }
		    }

		    // Если ни одно из правил не сработало, выбрасываем исключение
		    throw new \Exception("Invalid move for Pawn");
	    }
    }

    public function dump() {
        for ($y = 8; $y >= 1; $y--) {
            echo "$y ";
            for ($x = 'a'; $x <= 'h'; $x++) {
                if (isset($this->figures[$x][$y])) {
                    echo $this->figures[$x][$y];
                } else {
                    echo '-';
                }
            }
            echo "\n";
        }
        echo "  abcdefgh\n";
    }
}
