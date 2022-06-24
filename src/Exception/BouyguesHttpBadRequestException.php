<?php


namespace Bboxlab\Moselle\Exception;

final class BouyguesHttpBadRequestException extends \Exception
{
    /** Default error source when an error occurs, ie: your e-commerce backend */
    const DEFAULT_SOURCE = 'default';

    /** The error occurs on bt api */
    const BT_SOURCE = 'bt';

    /** A more detailed description of the error */
    private string $description;

    /** Some complementary arguments regarding the error */
    private array $parameters;

    /** Source describes where the error occurs first  */
    private string $source;

    public function __construct(
        ?string $message = '',
        \Throwable $previous = null,
        int $code = 0,
        array $headers = [],
        ?string $description = '',
        array $parameters = [],
        string $source = self::DEFAULT_SOURCE
    )
    {
        parent::__construct($message, $previous, $code, $headers);
        $this->setDescription($description);
        $this->setParameters($parameters);
        $this->setSource($source);
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @param string $source
     */
    public function setSource(string $source): void
    {
        $this->source = $source;
    }

}
