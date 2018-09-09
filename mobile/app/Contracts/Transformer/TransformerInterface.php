<?php
//多点乐资源
namespace app\contracts\transformer;

interface TransformerInterface
{
	public function transformCollection(array $map);

	public function transform(array $map);
}


?>
