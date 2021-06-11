<?php

namespace App\Serializer;

use App\Entity\Product;
use App\Entity\Customer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class ItemNormalizer implements ContextAwareNormalizerInterface
{
    private $router;
    private $normalizer;

    public function __construct(UrlGeneratorInterface $router, ObjectNormalizer $normalizer)
    {
        $this->router = $router;
        $this->normalizer = $normalizer;
    }

    public function normalize($item, string $format = null, array $context = [])
    {
        $data = $this->normalizer->normalize($item, $format, $context);

        $routeName = '';
        if($item instanceof Product) 
            $routeName = 'get_product';
        
        if($item instanceof Customer)
            $routeName = 'get_customer';
        
        $data['link']['self'] = $this->router->generate($routeName, [
            'id' => $item->getId(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []):bool
    {
        return ($data instanceof Product) || ($data instanceof Customer);
    }
}
