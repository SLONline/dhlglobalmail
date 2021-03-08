<?php


namespace SLONline\DHLGlobalMail\Exception;


class DHLGlobalMailRequestException extends DHLGlobalMailClientException
{
    const CODE_UNKNOWN = 0;
    const CODE_UNAUTHORIZED = 1;
    const CODE_CONNECTION_FAILED = 2;
    const CODES = [
        self::CODE_UNKNOWN,
        self::CODE_UNAUTHORIZED,
        self::CODE_CONNECTION_FAILED,
    ];
    
    /** @var int|null */
    protected $dhlCode;
    
    /** @var string|null */
    protected $dhlMessage;
    
    public function __construct(
        string $message = '',
        int $code = DHLGlobalMailRequestException::CODE_UNKNOWN,
        \Throwable $previous = null,
        ?int $dhlCode = null,
        ?string $dhlMessage = null
    ) {
        parent::__construct($message, $code, $previous);
        
        $this->dhlCode = $dhlCode;
        $this->dhlMessage = $dhlMessage;
    }
    
    /**
     * Returns the code reported by DHL Global Mail when available. This usually equals the HTTP status code.
     *
     * @return int|null
     */
    public function getDHLCode(): ?int
    {
        return $this->dhlCode;
    }
    
    /**
     * Returns the error message reported by DHL Global Mail when available.
     *
     * @return string|null
     */
    public function getDHLMessage(): ?string
    {
        return $this->dhlMessage;
    }
}