<?php

session_start();

$old = '';
$new = '';

if (
    (isset($_REQUEST['old']) && (!empty($_REQUEST['old']))) ||
    (isset($_REQUEST['new']) && (!empty($_REQUEST['new'])))
) {
    $old = $_REQUEST['old'];
    $new = $_REQUEST['new'];

    $_SESSION['old'] = $old;
    $_SESSION['new'] = $new;

    function textLines($str)
    {
        $line = explode(PHP_EOL, $str);
        return $line;
    }

    $leftTextLines = textLines($old);
    $rightTextLines = textLines($new);

    function lineChars($lines)
    {
        $chars = [];
        foreach ($lines as $key => $lineStr) {
            $chars[] = str_split($lineStr);
        }
        return $chars;
    }

    $leftTextLineChars = lineChars($leftTextLines);
    $rightTextLineChars = lineChars($rightTextLines);

    /**
     * @param $linesCharsOne
     * @param $linesCharsTwo
     * @param $k
     * @param $oldLinesChars
     * @param $newLinesChars
     * @return array|int
     */

    function compareLine(
        $linesCharsOne,
        $linesCharsTwo,
        $k,
        $oldLinesChars,
        $newLinesChars
    ) {
        $resultOld = '';
        $resultNew = '';

        $lines = count($linesCharsOne) >= count($linesCharsTwo) ?
            count($linesCharsOne) : count($linesCharsTwo);

        if ($k == $lines) {
            if ($lines != 0) {
                $allLinesChars[] = $oldLinesChars;
                $allLinesChars[] = $newLinesChars;
                return $allLinesChars;
            }
            return 1;
        }

        $linesCharsOne[$k] = $linesCharsOne[$k] ?? [];
        $linesCharsTwo[$k] = $linesCharsTwo[$k] ?? [];

        $leftLine = count($linesCharsOne[$k]);
        $rightLine = count($linesCharsTwo[$k]);

        $linesChars = $leftLine >= $rightLine ? $leftLine : $rightLine;
        $mismatchCharacters = $linesChars;
        $i = 0;
        $j = 0;

        while (($i < $leftLine) && isset($linesCharsTwo[$k][$j])) {
            if ($linesCharsOne[$k][$i] == $linesCharsTwo[$k][$j]) {
                $mismatchCharacters--;
                $i++;
                $j++;
            } elseif ($leftLine < $rightLine) {
                $j++;
            } elseif ($leftLine > $rightLine) {
                $i++;
            } else {
                $i++;
                $j++;
            }
        }

        if (empty($linesCharsTwo[$k][0]) && empty($linesCharsOne[$k][0])) {
            $resultNew = '';
            $resultOld = '';
        } elseif (empty($linesCharsOne[$k][0]) ) {
            $resultNew = '+' . $resultNew;
            $resultOld = '';
        } elseif (empty($linesCharsTwo[$k][0]) ) {
            $resultOld = '-' . $resultOld;
            $resultNew = '';
        } else {
            if ((($mismatchCharacters * 100) / $linesChars) > 30) {
                $resultOld .= '-' . $resultOld;
                $resultNew .= '+' . $resultNew;
            } elseif (
                (($mismatchCharacters * 100) / $linesChars) > 0 &&
                (($mismatchCharacters * 100) / $linesChars) <= 30) {
                $resultOld .= '!=' . $resultOld;
                $resultNew .= '!=' . $resultNew;
            }
        }

        $resultOld .= implode($linesCharsOne[$k]) . '<br>';
        $resultNew .= implode($linesCharsTwo[$k]) . '<br>';
        $oldLinesChars[] = $resultOld;
        $newLinesChars[] = $resultNew;

        $k++;
        return compareLine(
            $linesCharsOne,
            $linesCharsTwo,
            $k,
            $oldLinesChars,
            $newLinesChars
        );
    }

    $resultLinesChars = compareLine(
        $leftTextLineChars,
        $rightTextLineChars,
        $k = 0,
        $oldLinesChars = [],
        $newLinesChars = []
    );

}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

    <title>Task N1 </title>
</head>
<body>
<div class="container">
    <form action="diff_checker.php" method="POST">
        <div class="row">
            <div class="col-lg-6">
                <label for="oldText">Original Text</label>
                <textarea class="form-control" name="old" id="oldText" cols="30" rows="10" style="width:100%"><?= $_SESSION['old'] ?? '' ?></textarea>
            </div>

            <div class="col-lg-6">
                <div class="form-group">
                    <label for="newText">Changed Text</label>
                    <textarea class="form-control" name="new" id="newText" cols="30" rows="10" style="width:100%"><?= $_SESSION['new'] ?? '' ?></textarea>
                </div>
            </div>


        </div>

        <button type="sumbit" class="btn btn-primary" >Send</button>
    </form>
    <div class="row mt-5">
        <div class="col-sm-6">
            <div class="card text-black bg-light  mb-3" style="max-width: 100%;">
                <div class="card-body" style="max-width: 100%">
                    <h5 class="card-title"></h5>
                    <p class="card-text">
                        <?php foreach ($resultLinesChars[0] ?? [] as $key => $item){echo $key+1 . '. ' . $item;} ?>
                    </p>
                </div>
            </div>

        </div>

        <div class="col-sm-6">
            <div class="card text-black bg-light mb-3" style="max-width: 100%;">
                <div class="card-body" style="max-width: 100%">
                    <h5 class="card-title"></h5>
                    <p class="card-text">
                        <?php foreach ($resultLinesChars[1] ?? [] as $key => $item){echo $key+1 . '. ' . $item;} ?>
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>
</body>