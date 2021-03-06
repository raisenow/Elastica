<?php

namespace Elastica\Bulk;

use Elastica\Response as BaseResponse;

class ResponseSet extends BaseResponse implements \Iterator, \Countable
{
    /**
     * @var Response[]
     */
    protected $_bulkResponses = [];

    /**
     * @var int
     */
    protected $_position = 0;

    /**
     * @param BaseResponse $response
     * @param Response[]   $bulkResponses
     */
    public function __construct(BaseResponse $response, array $bulkResponses)
    {
        parent::__construct($response->getData());

        $this->_bulkResponses = $bulkResponses;
    }

    /**
     * @return Response[]
     */
    public function getBulkResponses(): array
    {
        return $this->_bulkResponses;
    }

    /**
     * Returns first found error.
     *
     * @return string
     */
    public function getError(): string
    {
        foreach ($this->getBulkResponses() as $bulkResponse) {
            if ($bulkResponse->hasError()) {
                return $bulkResponse->getError();
            }
        }

        return '';
    }

    /**
     * Returns first found error (full array).
     *
     * @return array|string
     */
    public function getFullError()
    {
        foreach ($this->getBulkResponses() as $bulkResponse) {
            if ($bulkResponse->hasError()) {
                return $bulkResponse->getFullError();
            }
        }

        return '';
    }

    /**
     * @return bool
     */
    public function isOk(): bool
    {
        foreach ($this->getBulkResponses() as $bulkResponse) {
            if (!$bulkResponse->isOk()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function hasError(): bool
    {
        foreach ($this->getBulkResponses() as $bulkResponse) {
            if ($bulkResponse->hasError()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Response
     */
    public function current(): Response
    {
        return $this->_bulkResponses[$this->key()];
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        ++$this->_position;
    }

    /**
     * {@inheritdoc}
     */
    public function key(): int
    {
        return $this->_position;
    }

    /**
     * {@inheritdoc}
     */
    public function valid(): bool
    {
        return isset($this->_bulkResponses[$this->key()]);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->_position = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return count($this->_bulkResponses);
    }
}
