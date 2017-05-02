<?php
function zscore($percentile){
    $z=0;
    if($percentile>.89){
         switch ($percentile) {
             case '.90':
                 $z=1.2816;
                 break;
             case '.91':
                 $z=1.3407;
                 break;
             case '.92':
                 $z=1.4050;
                 break;
             case '.93':
                 $z=1.4757;
                 break;
             case '.94':
                 $z=1.5547;
                 break;
             case '.95':
                 $z=1.6448;
                 break;
             case '.96':
                 $z=1.7506;
                 break;
             case '.97':
                 $z=1.8807;
                 break;
             case '.98':
                 $z=2.0537;
                 break;
             case '.99':
                 $z=2.3263;
                 break;
             default:
                 $z=0;
                 break;
         }
     }
     return $z;
}

?>
