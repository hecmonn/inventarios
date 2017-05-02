<?php
require_once './z_table.php';
function demanda_promedio(array $demanda){
    return array_sum($demanda)/count($demanda);
}
function pretty_number($number){
    return number_format($number,2,'.','');
}

function demanda_promedio_lt($dp,$tao){
    return $dp*$tao;
}

function eoq_periodo($a,$d,$h){
    return sqrt((2*$a*$d)/$h);
}

function costo_eoq(array $datos){
    //costo modelo eoq
}
function std_deviation(array $a, $sample = false) {
    $n = count($a);
    if ($n === 0) {
        trigger_error("The array has zero elements", E_USER_WARNING);
        return false;
    }
    if ($sample && $n === 1) {
        trigger_error("The array has only 1 element", E_USER_WARNING);
        return false;
    }
    $mean = array_sum($a) / $n;
    $carry = 0.0;
    foreach ($a as $val) {
        $d = ((double) $val) - $mean;
        $carry += $d * $d;
    };
    if ($sample) {
       --$n;
    }
    return sqrt($carry / $n);
}
function modelo_eoq(array $datos){
    $a=(float)$datos["costo_pedido"];
    $c=(float)$datos["costo_unidad"];
    $i=(float)$datos["interes"];
    $h=$i*$c;
    $demanda_prom=demanda_promedio($datos["demanda"]);
    $demanda_24=$demanda_prom*24;
    $q_asterico=eoq_periodo($a,$demanda_24,$h);
    $t_modelo=$q_asterico/$demanda_24;
    $f_modelo=1/$t_modelo;
    $modelo_eoq=["q"=>$q_asterico,"t"=>$t_modelo,"f"=>$f_modelo];
    return $modelo_eoq;
}
function cme(array $datos){
    $sumsq=0;
    for ( $i = 0; $i <= count($datos)-1; $i++ ) {
        $sumsq += $datos[$i] * $datos[$i];
    }
    for ($i=0; $i < count($datos); $i++) {
        if($datos[$i]==0){
            unset($datos[$i]);
        }
    }
    $cme=$sumsq/count($datos);
    return $cme;
}
function modelo_qr(array $datos){
    $a=(float)$datos["costo_pedido"];
    $c=(float)$datos["costo_unidad"];
    $i=(float)$datos["interes"];
    $h=$i*$c;
    $tao=(float)$datos["lt"];
    $fill_rate=(float)$datos["fr"];
    $sigma_dem=pretty_number(std_deviation($datos["demanda"],true));
    $demanda_prom=demanda_promedio($datos["demanda"]);
    $demanda_tao=pretty_number($demanda_prom*$tao);
    $sigma_tao=pretty_number($sigma_dem*sqrt($tao));
    $q=ceil(sqrt((2*$a*$demanda_prom)/$h));
    $t=pretty_number($q/$demanda_prom);


    //CSL
    $alpha=(float)$datos["alpha"];
    $z=zscore($alpha);
    $s=ceil($sigma_tao*$z);
    $r=ceil($s+$demanda_tao);

    //FILL RATE
    $beta=(float)$datos["fr"];
    $lz=((1-$beta)*$q)/($sigma_tao);
    $z_fill_rate=zscore($lz);
    $s_fill_rate=$sigma_tao*$z_fill_rate;
    $r_fill_rate=$demanda_tao+$s_fill_rate;
    $no_surtidas=pretty_number((1-$beta)*$demanda_prom);
    $costos=costos(["demanda_prom"=>$demanda_prom,"costo_unidad"=>$c,"costo_pedido"=>$a,"ret"=>$datos["ret"],"q"=>$q]);
    //die(print_r($costos));
    $modelo_qr=[
        "nombre"=>"qr",
        "demanda_promedio"=>$demanda_prom,
        "demanda_tao"=>$demanda_tao,
        "q"=>$q,
        "t"=>$t,
        "sigma_tao"=>$sigma_tao,
        "z"=>$z,
        "s_csl"=>$s,
        "r_csl"=>$r,
        "lz"=>$lz,
        "beta"=>$beta,
        "z_fill"=>$z_fill_rate,
        "s_fill"=>$s_fill_rate,
        "r_fill"=>$r_fill_rate,
        "no_surtidas"=>$no_surtidas,
        "costo_pedido"=>$costos["pedido"],
        "costo_retencion"=>$costos["retencion"],
        "costo_total"=>$costos["total"]
    ];
    return $modelo_qr;
}

function costos(array $datos){
    //Costo anual
    $q=$datos["q"];
    $demanda_anual=$datos["demanda_prom"]*12;
    $costo_unidad=$datos["costo_unidad"];
    $p_retencion=$datos["ret"];
    $a=$datos["costo_pedido"];
    $costo_anual_pedido=pretty_number($a*($demanda_anual/$q));
    $costo_anual_retencion=pretty_number(($q/2)*$costo_unidad*$p_retencion);
    $costo_total=$costo_anual_pedido+$costo_anual_retencion;
    return ["pedido"=>$costo_anual_pedido,"retencion"=>$costo_anual_retencion,"total"=>$costo_total];
}
function promedios_moviles(array $datos){
    //N=2
    $demanda=$datos["demanda"];
    for ($i=0; $i < count($demanda)-1 ; $i++) {
        $mt=($demanda[$i]+$demanda[$i+1])/2;
        $mt_arr[$i]=$mt;
    }
    for ($i=0; $i < count($mt_arr)-1 ; $i++) {
        $ft=$mt_arr[$i];
        $ft_arr[$i]=$ft;
    }
    array_unshift($ft_arr,0,0);
    array_unshift($mt_arr,0);
    for ($i=0; $i < count($demanda) ; $i++) {
        $dt=$demanda[$i];
        $ft=$ft_arr[$i];
        $et_arr[$i]=$dt-$ft;
    }
    $et_arr[0]=0;
    $et_arr[1]=0;
    $cme_2=cme($et_arr);
    $n2=[$mt_arr,$ft_arr,$et_arr];
    //N=3
    for ($i=0; $i < count($demanda) ; $i++) {
        $mt=($demanda[$i]+$demanda[$i+1]+$demanda[$i+2])/3;
        $mt_arr_3[$i]=$mt;
    }
    for ($i=0; $i < count($mt_arr); $i++) {
        $ft=$mt_arr_3[$i];
        $ft_arr_3[$i]=$ft;
    }
    array_unshift($ft_arr_3,0,0,0);
    array_unshift($mt_arr_3,0,0);
    for ($i=0; $i < count($demanda) ; $i++) {
        $dt=$demanda[$i];
        $ft=$ft_arr_3[$i];
        $et_arr_3[$i]=$dt-$ft;
    }
    $et_arr_3[0]=0;
    $et_arr_3[1]=0;
    $et_arr_3[2]=0;
    $et_arr_3[3]=0;
    $cme_3=cme($et_arr_3);
    $n3=[$mt_arr_3,$ft_arr_3,$et_arr_3];
    //N=5
    for ($i=0; $i < count($demanda) ; $i++) {
        $mt=($demanda[$i]+$demanda[$i+1]+$demanda[$i+2]+$demanda[$i+3]+$demanda[$i+4])/5;
        $mt_arr_5[$i]=$mt;
    }
    for ($i=0; $i < count($mt_arr); $i++) {
        $ft=$mt_arr_5[$i];
        $ft_arr_5[$i]=$ft;
    }
    array_unshift($ft_arr_5,0,0,0,0,0);
    array_unshift($mt_arr_5,0,0,0,0);
    for ($i=0; $i < count($demanda) ; $i++) {
        $dt=$demanda[$i];
        $ft=$ft_arr_5[$i];
        $et_arr_5[$i]=$dt-$ft;
    }
    $et_arr_5[0]=0;
    $et_arr_5[1]=0;
    $et_arr_5[2]=0;
    $et_arr_5[3]=0;
    $et_arr_5[4]=0;
    $cme_5=cme($et_arr_5);
    $n5=[$mt_arr_5,$ft_arr_5,$et_arr_5];

    for ($i=0; $i <count($et_arr) ; $i++) {
        $etdt=abs($et_arr[$i]/$demanda[$i]);
        $etdt_arr[$i]=$etdt;
    }
    for ($i=0; $i < count($etdt_arr) ; $i++) {
        if($etdt_arr[$i]==0)
            unset($etdt_arr[$i]);
    }
    for ($i=0; $i <count($et_arr_3) ; $i++) {
        $etdt=abs($et_arr_3[$i]/$demanda[$i]);
        $etdt_arr_3[$i]=$etdt;
    }
    for ($i=0; $i < count($etdt_arr_3) ; $i++) {
        if($etdt_arr_3[$i]==0)
            unset($etdt_arr_3[$i]);
    }
    for ($i=0; $i <count($et_arr_5) ; $i++) {
        $etdt=abs($et_arr_5[$i]/$demanda[$i]);
        $etdt_arr_5[$i]=$etdt;
    }
    for ($i=0; $i < count($etdt_arr_5) ; $i++) {
        if($etdt_arr_5[$i]==0)
            unset($etdt_arr_5[$i]);
    }

    $mape=array_sum($etdt_arr)/(count($etdt_arr));
    $mape_3=array_sum($etdt_arr_3)/(count($etdt_arr_3));
    $mape_5=array_sum($etdt_arr_5)/(count($etdt_arr_5));
    if($mape<$mape_3 && $mape < $mape_5)
        $mape_final=["nombre"=>"promedios moviles","mape"=>$mape,"mt"=>$mt_arr,"ft"=>$ft_arr,"et"=>$et_arr,"etdt"=>$etdt_arr,"cme"=>$cme];
    elseif($mape_3 <$mape && $mape_3<$mape_5)
        $mape_final=["nombre"=>"promedios moviles","mape"=>$mape_3,"mt"=>$mt_arr_3,"ft"=>$ft_arr_3,"et"=>$et_arr_3,"etdt"=>$etdt_arr_3,"cme"=>$cme_3];
    elseif($mape_5 <$mape && $mape_5<$mape_3)
        $mape_final=["nombre"=>"promedios moviles","mape"=>$mape_5,"mt"=>$mt_arr_5,"ft"=>$ft_arr_5,"et"=>$et_arr_5,"etdt"=>$etdt_arr_5,"cme"=>$cme_5];
    return $mape_final;

}
function suavizacion_exp(array $datos){
    $demanda=$datos["demanda"];
    $alpha=$datos["alpha"];
    for ($i=0; $i < count($demanda) ; $i++) {
        $st=$i==0?$demanda[$i]:($alpha*$demanda[$i]+(1-$alpha)*$demanda[$i-1]);
        $st_arr[$i]=$st;
    }
    for ($i=0; $i < count($st_arr) ; $i++) {
        $ft=$i==0?0:$st_arr[$i-1];
        $ft_arr[$i]=$ft;
    }
    for ($i=0; $i < count($ft_arr); $i++) {
        $et=$i==0?0:$demanda[$i]-$ft_arr[$i];
        $et_arr[$i]=$et;
    }
    for ($i=0; $i <count($et_arr) ; $i++) {
        $etdt=abs($et_arr[$i]/$demanda[$i]);
        $etdt_arr[$i]=$etdt;
    }
    $cme=cme($et_arr);
    $mape=array_sum($etdt_arr)/(count($etdt_arr)-1);
    return ["nombre"=>"suavizacion exponencial","mape"=>$mape,"st"=>$st_arr,"ft"=>$ft_arr,"et"=>$et_arr,"etdt"=>$etdt_arr,"cme"=>$cme];

}
function qr_table(array $datos){
    $net_invertory=$datos["q"];
    $r=$datos["r"];
    $demanda=array_unshift($datos["demanda"],0);
    for ($i=0; $i < count($demanda) ; $i++) {
        $demnada=$demanda[$i];
        //$net_inventory=

    }
}
function correr_modelo(array $datos){
    $modelo_qr=modelo_qr($datos);
    $promedios_moviles=promedios_moviles($datos);
    $suavizacion_exp=suavizacion_exp($datos);
    if($promedios_moviles["mape"]>$suavizacion_exp["mape"]){
        $pronostico=$suavizacion_exp;
    } else {
        $pronostico=$promedios_moviles;
    }
    return ["modelo"=>$modelo_qr,"pronostico"=>$pronostico];
}
?>
