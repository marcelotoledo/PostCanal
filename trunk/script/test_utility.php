#!/usr/bin/env php
<?php

require "../application/console.php";

$html = <<<EOT
xclr8r writes "Eric Massa, a congressman representing a district in western New York, has a bill ready that would start treating Internet providers like a utility and stop the use of caps. Nearby locales have been used as test beds for the new caps, so this may have made the constituents raise the issue with their representative."<p><a href="http://news.slashdot.org/story/09/06/18/1521237/Bill-Ready-To-Ban-ISP-Caps-In-the-US?from=rss"><img src="http://slashdot.org/slashdot-it.pl?from=rss&amp;op=image&amp;style=h0&amp;sid=09/06/18/1521237" /></a></p><p><a href="http://news.slashdot.org/story/09/06/18/1521237/Bill-Ready-To-Ban-ISP-Caps-In-the-US?from=rss">Read more of this story</a> at Slashdot.</p>
<p><a href="http://feedads.g.doubleclick.net/~at/IJwh0_8_PLqdZAGlHefrlf3bt1M/0/da"><img border="0" ismap="true" src="http://feedads.g.doubleclick.net/~at/IJwh0_8_PLqdZAGlHefrlf3bt1M/0/di" /></a><br />
<a href="http://feedads.g.doubleclick.net/~at/IJwh0_8_PLqdZAGlHefrlf3bt1M/1/da"><img border="0" ismap="true" src="http://feedads.g.doubleclick.net/~at/IJwh0_8_PLqdZAGlHefrlf3bt1M/1/di" /></a></p><img height="1" src="http://feeds2.feedburner.com/~r/Slashdot/slashdot/~4/ZbsetFjjqiU" width="1" />
EOT;

/*
$html = <<<EOT
<p><center><img src="http://img.efetividade.net/img/xtra/nokia-n810-fisl.jpg" title="nokia n810 fisl.jpg - fonte: nokia n819.jpg (imagem JPEG, 496x363 pixels) (http://www.lsw.uni heidelberg.de/users/josulliv/images/stories/nokia n819.jpg) " /></center></p>
<p><b>Ganhe um Nokia N810 (e mais) no desafio Qt/OpenBossa no FISL</b>: &ldquo;Você possui uma boa idéia? Conhece a biblioteca Qt? Gostaria de ser premiado por isso?  O INdT (Instituto Nokia de Tecnologia), hoje um dos principais centros de excelência e desenvolvimento da biblioteca Qt no Brasil, promove o 1o Desafio Qt/openBossa.  Buscamos desenvolvedores dispostos a colocar em prática suas idéias e criar aplicações Qt projetadas para os dispositivos móveis da Nokia. Os participantes deverão criar aplicações inovadoras que serão avaliadas e premiadas durante a décima edição do Fórum Internacional de Software Livre (FISL).  Premiação O vencedor do 1o Desafio Qt/openBossa será premiado com 1 (um) Internet Tablet Nokia N810, 1 (um) conjunto de caixas de som bluetooth Nokia MD-7 e convite, com passagem e hospedagem pagos por uma semana para conhecer as instalações do INdT em Recife, onde irá participar de um curso de aprofundamento nas mais novas tecnologias desenvolvidas na biblioteca Qt.   Mais informações em: http://openbossa.org/fisl/index.html  Obrigado,&rdquo; [Enviado por Artur Duque de Souza (morpheuz&Theta;gmail&#183;com) - <a href="http://openbossa.org/fisl/index.html">referência</a> (openbossa.org).]</p>
EOT;
*/

A_Utility::keywords($html);

echo $html . "\n";
