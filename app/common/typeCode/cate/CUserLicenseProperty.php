<?php
/**
 * Created by PhpStorm.
 * User: 刘彪
 * Date: 2019/12/25
 * Time: 14:37
 */

namespace app\common\typeCode\cate;


class CUserLicenseProperty implements \app\common\typeCode\CateImpl,\app\common\typeCode\CacheImpl
{
    private $type = 8;

    private $cacheName = 'cate_user_license_property';

    private $Level = 1;

    private $masterId = 0;

    private $issetAttr = false;

    public function getCateType(): int
{
    return $this->type;
}

    public function getLevelType(): int
{
    return $this->Level;
}

    public function getCacheName(): string
{
    return $this->cacheName;
}

    public function setMasterId($id)
{
    $this->masterId = $id;
}

    public function getMasterId(): int
{

    return $this->masterId;
}

    /**
     * @return bool
     */
    public function issetAttr(): bool
{
    return $this->issetAttr;
}


}