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
            new TwigFunction('cryptoFluctuation', [$this, 'getCryptoFluctuation']),
        ];
    }

    public function getFluctuation($contrat): string // should be Contract type
    {
        $currentPriceQuantity = $contrat->getCrypto()->getCurrentPrice() * $contrat->getQuantity();
        return $this->fluctuationSwitch($currentPriceQuantity, $contrat->getPrice());
    }

    public function getBenefit(array $contrats): int
    {
        $benefit = 0;
        foreach($contrats as $contrat)
        {
            $benefit += $contrat->getCrypto()->getCurrentPrice() * $contrat->getQuantity() - $contrat->getPrice();
        }
        return $benefit;
    }

    public function getCryptoFluctuation($crypto, array $sumCrypto): string
    { 
        $index = array_search($crypto->getId(), array_column($sumCrypto, 'cryptoId'));
        if($index !== false){
            $aSumCrypto = $sumCrypto[$index];
        }
        else {
            return false;
        }
        $currentPriceQuantity = $crypto->getCurrentPrice() * $aSumCrypto['quantity'];

        return $this->fluctuationSwitch($currentPriceQuantity, intval($aSumCrypto['price']));
    }

    private function fluctuationSwitch(int $currentPriceQuantity, int $price) : string
    {
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
        // if ($nbrI ==1)
        // {
        //     return '<div class="material-icons text-light '. $class .'">'. $icon .'</div>';
        // }
        // else
        // {
        //     return '<div class="d-flex justify-content-end text-light">
        //         <div class="material-icons '. $class .'">'. $icon .'</div>
        //         <div class="material-icons '. $class .'">'. $icon .'</div>
        //     </div>
        //     ';
        // }
    }
}
