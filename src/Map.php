<?php

namespace Surzhikov\StaticMapUrlGenerator;

class Map
{
	private $provider;
	private string|null $apiKey = null;
	private string|null $mapStyle = null;
	private int $width = 200;
	private int $height = 100;
	private bool $retina = false;
	private bool $attribution = true;

	private string|null $position = null;
	private array $center = [0,0,1];
	private array $bounds = [-10,-10,10,10];
	private float|null $padding = null;

	private array $polylines = [];
	private array $markers = [];

	/**
	 * Class construcotr
	 */
	public function __construct($provider)
	{
		if (in_array($provider, ['maptiler']) === false) {
			throw new \Exception('Now we support only maptiler provider');
		}
		$this->provider = $provider;
	}

	/**
	 * Static function to init instance 
	 */
	public static function provider($provider)
	{
		$map = new Map($provider);
		return $map;
	}

	/**
	 * Set API key 
	 */
	public function apiKey($apiKey)
	{
		if (in_array($this->provider, ['maptiler'])) {
			$this->apiKey = $apiKey;
			return $this;
		}
		throw new \Exception('Selected provider does not support API key');
	}

	/**
	 * Set width of image 
	 */
	public function width(int $width)
	{
		$this->width = $width;
		return $this;
	}

	/**
	 * Set height of image 
	 */
	public function height(int $height)
	{
		$this->height = $height;
		return $this;
	}

	/**
	 * Set retina param
	 */
	public function retina(bool $retina)
	{
		if (in_array($this->provider, ['maptiler'])) {
			$this->retina = $retina;
			return $this;
		}
		throw new \Exception('Selected provider does not support retina maps');
	}

	/**
	 * Set map style 
	 */
	public function mapStyle(string $mapStyle)
	{
		if (in_array($this->provider, ['maptiler'])) {
			$this->mapStyle = $mapStyle;
			return $this;
		}
		throw new \Exception('Selected provider does not support maps styles');	
	}

	/**
	 * Autofitting map position
	 */
	public function autoFit()
	{
		if (in_array($this->provider, ['maptiler'])) {
			$this->position = 'autofit';
			return $this;
		}
		throw new \Exception('Selected provider does not support map autofit');
	}

	/**
	 * Set center of map 
	 */
	public function centered($lng, $lat, $zoom)
	{
		if (in_array($this->provider, ['maptiler'])) {
			$this->position = 'center';
			$this->center = [$lng, $lat, $zoom];
			return $this;
		}
		throw new \Exception('Selected provider does not support maps centered');	
	}

	/**
	 * Set bounds of map 
	 */
	public function bounds($minx, $miny, $maxx, $maxy)
	{
		if (in_array($this->provider, ['maptiler'])) {
			$this->position = 'bounds';
			$this->bounds = [$minx, $miny, $maxx, $maxy];
			return $this;
		}
		throw new \Exception('Selected provider does not support maps bounds');	
	}

	/**
	 * Set padding of map 
	 */
	public function padding(float $padding)
	{
		if (in_array($this->provider, ['maptiler'])) {
			$this->padding = $padding;
			return $this;
		}
		throw new \Exception('Selected provider does not support maps padding');	
	}

	/**
	 * Set attribution
	 */
	public function attribution(bool $attribution)
	{
		$this->attribution = $attribution;
		return $this;
	}

	/**
	 * Add Polyline
	 */
	public function addPolyline(
		array $points,
		int $strokeWidth = 1,
		string $strokeColor = 'rgba(0,0,255,0.5)',
		string $fillColor = 'rgba(0,0,255,0.2)',
		bool $asEnc = true
	)
	{
		if (in_array($this->provider, ['maptiler'])) {
			$this->polylines[]= [
				'points' => $points,
				'strokeColor' => $strokeColor,
				'strokeWidth' => $strokeWidth,
				'fillColor' => $fillColor,
				'asEnc' => $asEnc
			];
			return $this;
		}

		throw new \Exception('Selected provider does not support polylines');

		return $this;
	}


	/**
	 * Add markers
	 */
	public function addMarker(
		array $point,
		string|null $color = null,
		string|null $anchor = null,
		string|null $icon = null,
		int|null $scale = null,
	)
	{
		if (in_array($this->provider, ['maptiler'])) {
			$this->markers[]= [
				'point' => $point,
				'color' => $color,
				'anchor' => $anchor,
				'icon' => $icon,
				'scale' => $scale,
			];
			return $this;
		}

		throw new \Exception('Selected provider does not support markers');

		return $this;
	}


	public function url()
	{
		if ($this->provider == 'maptiler') {
			return $this->buildUrlForMaptiler();
		}
		throw new \Exception('Bad provider');
	}




	private function buildUrlForMaptiler()
	{
		if ($this->mapStyle == null) {
			throw new \Exception('Maptiler provider requires mapStyle');
		}

		$url = 'https://api.maptiler.com/maps/' . $this->mapStyle . '/static';

		if ($this->width == null) {
			throw new \Exception('Maptiler provider requires width');
		}
		if ($this->height == null) {
			throw new \Exception('Maptiler provider requires height');
		}

		switch ($this->position) {
			case 'autofit':
				if (count($this->polylines) == 0 && count($this->markers) == 0) {
					throw new \Exception('Maptiler provider requires polylines or markers for using autofit position)');
				}
				$url.= '/auto';
				break;
			case 'center':
				$url.= '/' . implode(',', $this->center);
				break;
			case 'bounds':
				$url.= '/' . implode(',', $this->bounds);
				break;
			default:
				throw new \Exception('Maptiler provider requires position type (set autoFit / centered / bounds)');
				break;
		}


		// Name
		$url.= '/' . $this->width . 'x' . $this->height;
		if ($this->retina) {
			$url.= '@2x';
		}
		$url.= '.png?';


		if ($this->apiKey == null) {
			throw new \Exception('Maptiler provider requires apiKey');
		}
		$url.= 'key=' . $this->apiKey . '&';


		if ($this->padding != null) {
			$url.= 'padding=' . $this->padding . '&';
		}

		foreach ($this->polylines as $polyline) {

			$pathParams = [];
			$pathParams[]= 'stroke:' . $polyline['strokeColor'];
			$pathParams[]= 'width:' . $polyline['strokeWidth'];
			$pathParams[]= 'fill:' . $polyline['fillColor'];

			if ($polyline['asEnc']) {
				$pathParams[]= 'enc:' . GooglePolylineEncoder::encode($polyline['points']);
			} else {
				foreach ($polyline['points'] as $point) {
					$pathParams[]= implode(',', $point);
				}
			}

			$url.= 'path=' . implode('|', $pathParams) . '&';
		}		

		foreach ($this->markers as $marker) {
			$pathParams = [];
			$point = implode(',', $marker['point']);

			if ($marker['color'] != null) {
				$point.= ',' . $marker['color'];
			}
			$pathParams[]= $point;

			if ($marker['anchor'] != null) {
				$pathParams[]= 'anchor:' . $marker['anchor'];
			}
			if ($marker['icon'] != null) {
				$pathParams[]= 'icon:' . urlencode($marker['icon']);
			}
			if ($marker['scale'] != null) {
				$pathParams[]= 'scale:' . $marker['scale'];
			}

			$url.= 'markers=' . implode('|', $pathParams). '&';
		}

		$url.= 'attribution=' . intval($this->attribution);

		return $url;
	}


}
