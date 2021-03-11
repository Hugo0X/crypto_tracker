<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('fluctuation', [$this, 'getFluctuation']),
            new TwigFunction('benefit', [$this, 'getBenefit']),
        ];
    }

    public function getFluctuation($contrat): string // should be Contract type
    {
        $currentPriceQuantity = $contrat->getCurrentPrice() * $contrat->getQuantity();
        $price = $contrat->getPrice();
        switch ($currentPriceQuantity){
            case $currentPriceQuantity < $price * 2:
                return $this->getHtmlIcon(2, 'rotate270');
            case $currentPriceQuantity < $price:
                return $this->getHtmlIcon(1, 'rotate270');
            case $currentPriceQuantity > $price * 2:
                return $this->getHtmlIcon(2, 'rotate120');
            case $currentPriceQuantity > $price:
                return $this->getHtmlIcon(1, 'rotate120');
            case $currentPriceQuantity == $price:
                return $this->getHtmlIcon(1);
            }
    }

    private function getHtmlIcon(int $nbrI, ?string $class) : string
    {
        if($class)
        {
            $icon = 'reply';
        }
        else
        {
            $icon = 'forward';
        }

        return str_repeat('<span class="material-icons text-light '. $class .'">'. $icon .'</span>', $nbrI);
    }

    public function getBenefit(array $contrats): int
    {
        $benefit = 0;
        foreach($contrats as $contrat)
        {
            $benefit += $contrat->getCurrentPrice() * $contrat->getQuantity() - $contrat->getPrice();
        }
        return $benefit;
    }
}
