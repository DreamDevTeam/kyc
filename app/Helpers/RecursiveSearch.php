<?php


namespace App\Helpers;


class RecursiveSearch
{

    private $resIndex = [];
    private $resIndexData = [];
    private $resIndexParent = [];

    /**
     * @param array $array
     * @param string $searchValue
     * @param string $flag
     *
     * k - search for key
     * v - search for value
     * a - search for key and value
     *
     * @return array|null
     */
    public function index(array $array, string $searchValue, string $flag = 'a'): array|null
    {
        foreach ($array as $key => $value) {
            if ($flag === 'k') {
                if ($key === $searchValue) {
                    $this->resIndex[] = [$key => $value];
                }
            }

            if ($flag === 'v') {
                if ($value === $searchValue) {
                    $this->resIndex[] = [$key => $value];
                }
            }

            if ($flag === 'a') {
                if ($key == $searchValue) {
                    $this->resIndex[] = [$key => $value];
                }
                if ($value == $searchValue) {
                    $this->resIndex[] = [$key => $value];
                }
            }

            if (is_array($value)) {
                $this->index($value, $searchValue);
            }
        }

        return match (true) {
            count($this->resIndex) > 1 => $this->resIndex,
            count($this->resIndex) === 1 => $this->resIndex[0],
            default => null
        };
    }

    /**
     * The function returns key data
     *
     * @param array $array
     * @param string $searchValue
     * @return array|null
     */
    public function indexData(array $array, string $searchValue): array|null
    {
        foreach ($array as $key => $value) {

            if ($key === $searchValue) {
                $this->resIndexData[] = [$key => $value];
            }

            if (is_array($value)) {
                $this->indexData($value, $searchValue);
            }
        }

        return match (true) {
            count($this->resIndexData) > 1 => $this->resIndexData,
            count($this->resIndexData) === 1 => $this->resIndexData[0],
            default => null
        };
    }

    /**
     * The function returns item's parent
     *
     * @param array $array
     * @param string $searchValue
     * @param array $parent
     * @return array|null
     */
    public function indexParent(array $array, string $searchValue, array $parent = []): array|null
    {
        foreach ($array as $key => $value) {

            if ($key === $searchValue) {
                $this->resIndexParent[] = $parent ?? $value[$key];
            }

            if (is_array($value)) {
                $this->indexParent(array:$value, searchValue:$searchValue, parent:$value);
            }
        }

        return match (true) {
            count($this->resIndexParent) > 1 => $this->resIndexParent,
            count($this->resIndexParent) === 1 => $this->resIndexParent[0],
            default => null
        };
    }
}
