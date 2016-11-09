<?php

namespace Innobotics;

/**
 * Image resize class.
 *
 * Usage:
 * <pre>
 *
 * $image = new \Innobotics\ImageResize();
 *
 * $image->setType('large', 640, 480);
 * $image->setType('medium', 320, 240);
 * $image->setType('thumbnail', 160, 120);
 *
 * $image->setSource('/home/notesz/images/0001.jpg');
 * $image->setTarget('/home/notesz/images/resized');
 *
 * $image->setSaveOriginal(false); //optional
 *
 * $image->setProgressive(false); // optional
 *
 * $image->setRetina(true); // optional
 *
 * $image->setPrefix('notesz'); // optional
 *
 * if ($image->resize() === true) {
 *     print 'Done.' . PHP_EOL;
 * } else {
 *    print 'Something wrong happened.' . PHP_EOL;
 * }
 *
 * $result = $image->getResult();
 * print_r($result);
 *
 * </pre>
 *
 * @copyright Copyright (c) 2016 Norbert Lakatos (http://innobotics.eu)
 * @author Norbert Lakatos <norbert@innobotics.eu>
 */
class ImageResize
{
    const STATUS_SUCCESS = 'success';

    const STATUS_ERROR = 'error';

    const PREFIX = '';

    const SEPARATOR = '_';

    const RETINA_POSTFIX = '@2x';

    const COMPRESSION_QUALITY = 75;

    /**
     * Types of the image.
     *
     * @var array
     */
    private $type;

    private $source;

    private $filename;

    private $target;

    private $prefix;

    private $status;

    private $message;

    private $saveOriginal;

    private $progressive;

    private $compression;

    private $retina;

    public function __construct()
    {
        $this->type = array();

        $this->source = null;

        $this->filename = null;

        $this->target = null;

        $this->prefix = self::PREFIX;

        $this->status = self::STATUS_ERROR;

        $this->message = '';

        $this->saveOriginal = true;

        $this->progressive = true;

        $this->compression = self::COMPRESSION_QUALITY;

        $this->retina = false;
    }

    /**
     * @param $key
     * @param $width
     * @param $height
     *
     * @return array
     */
    public function setType($key, $width, $height)
    {
        $type = $this->type;

        $type[$key] = array(
            'sizeWidth'  => $width,
            'sizeHeight' => $height
        );

        $this->type = $type;

        return $this->type;
    }

    /**
     * @return array
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param $source
     *
     * @return null
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this->source;
    }

    /**
     * @return null
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param $filename
     *
     * @return null
     */
    public function setFileName($filename)
    {
        $this->filename = $filename;

        return $this->filename;
    }

    /**
     * @return null
     */
    public function getFileName()
    {
        return $this->filename;
    }

    /**
     * @param $target
     *
     * @return null
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this->target;
    }

    /**
     * @return null
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param $prefix
     *
     * @return null
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this->prefix;
    }

    /**
     * @return null
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param $status
     *
     * @return string
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this->status;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param $message|array
     *
     * @return string|array
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this->message;
    }

    /**
     * @return string|array
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param $saveOriginal|bool
     *
     * @return bool
     */
    public function setSaveOriginal($saveOriginal)
    {
        $this->saveOriginal = $saveOriginal;

        return $this->saveOriginal;
    }

    /**
     * @return bool
     */
    public function getSaveOriginal()
    {
        return $this->saveOriginal;
    }

    /**
     * @param $progressive|bool
     *
     * @return bool
     */
    public function setProgressive($progressive)
    {
        $this->progressive = $progressive;

        return $this->progressive;
    }

    /**
     * @return bool
     */
    public function getProgressive()
    {
        return $this->progressive;
    }

    /**
     * @param $compression|int
     *
     * @return int
     */
    public function setCompression($compression)
    {
        $this->compression = $compression;

        return $this->compression;
    }

    /**
     * @return int
     */
    public function getCompression()
    {
        return $this->compression;
    }

    /**
     * @param $retina|bool
     *
     * @return bool
     */
    public function setRetina($retina)
    {
        $this->retina = $retina;

        return $this->retina;
    }

    /**
     * @return bool
     */
    public function getRetina()
    {
        return $this->retina;
    }

    /**
     * Resize
     *
     * @return bool
     */
    public function resize()
    {
        try {
            $resultMessage = array();

            // Set filename
            //@todo: check source
            if (empty($this->filename)) {
                $source = \explode('/', $this->source);
                $this->setFileName($source[\count($source)-1]);
            }

            // Define filename and extension
            //@todo: check filename
            $fileNameExtension = \substr($this->filename, \strrpos($this->filename, '.')+1);
            $fileName = \str_replace('.' . $fileNameExtension, '', $this->filename);

            // Define retina size images
            if ($this->retina === true) {
                foreach ($this->type as $key => $imageType) {
                    $this->setType($key . self::RETINA_POSTFIX, $imageType['sizeWidth']*2, $imageType['sizeHeight']*2);
                }
            }

            // Resize and save images
            //@todo: check type
            foreach ($this->type as $key => $imageType) {

                $img = new \Imagick($this->source);

                // Get exif and rotate the image
                $exif = \exif_read_data($this->source, 'IFD0');
                if (!empty($exif) && !empty($exif['Orientation'])) {
                    switch ($exif['Orientation']) {
                        case 8:
                            $img->rotateImage(new \ImagickPixel(), -90);
                            break;

                        case 3:
                            $img->rotateImage(new \ImagickPixel(), -180);
                            break;

                        case 6:
                            $img->rotateImage(new \ImagickPixel(), -270);
                            break;

                        default:
                            break;
                    }
                }

                // Get original size of the image
                $imageWidth = $img->getImageWidth();
                $imageHeight = $img->getImageHeight();

                // Resize the image
                if ($imageWidth > $imageHeight) {
                    $imageRotation = 'landscape';
                }
                elseif ($imageWidth < $imageHeight) {
                    $imageRotation = 'portrait';
                }
                else {
                    $imageRotation = 'square';
                }

                switch ($imageRotation) {
                    case 'landscape':
                        $img->resizeImage(
                            \round($imageWidth/($imageHeight/$imageType['sizeHeight'])),
                            $imageType['sizeHeight'],
                            \Imagick::FILTER_LANCZOS,
                            0.9,
                            true
                        );
                        break;

                    case 'portrait':
                        $img->resizeImage(
                            $imageType['sizeWidth'],
                            \round($imageHeight/($imageWidth/$imageType['sizeWidth'])),
                            \Imagick::FILTER_LANCZOS,
                            0.9,
                            true
                        );
                        break;

                    case 'square':
                        $img->resizeImage(
                            $imageType['sizeWidth'],
                            \round($imageHeight/($imageWidth/$imageType['sizeWidth'])),
                            \Imagick::FILTER_LANCZOS,
                            0.9,
                            true
                        );
                        break;

                    default:
                        break;
                }

                // Crop the image
                $img->cropImage(
                    $imageType['sizeWidth'],
                    $imageType['sizeHeight'],
                    0,
                    0
                );

                // Sharpen image
                $img->adaptiveSharpenImage(2, 1);

                // Set progressive
                if ($this->progressive === true) {
                    $img->setInterlaceScheme(\Imagick::INTERLACE_PLANE);
                }

                // Set compression quality
                $img->setCompressionQuality($this->compression);

                // Save
                $outFileName = (empty($this->prefix) ? '' : $this->prefix . self::SEPARATOR) . $fileName . self::SEPARATOR . $key . '.' . $fileNameExtension;
                @\unlink($this->target . '/' . $outFileName);
                $img->writeImage($this->target . '/' . $outFileName);

                $resultMessage[$key] = $outFileName;
            }

            // Save original image
            if ($this->saveOriginal === true) {

                // Save
                $outFileName = (empty($this->prefix) ? '' : $this->prefix . self::SEPARATOR) . $fileName . '.' . $fileNameExtension;
                @\unlink($this->target . '/' . $outFileName);
                \copy($this->source, $this->target . '/' . $outFileName);

                $resultMessage['original'] = $outFileName;
            }

            // Set status
            $this->setStatus(self::STATUS_SUCCESS);
            $this->setMessage(array('files' => $resultMessage));

            return true;
        } catch(\Exception $e) {

            // Set status
            $this->setStatus(self::STATUS_ERROR);
            $this->setMessage($e->getMessage());

            return false;
        }
    }

    /**
     * @return array
     */
    public function getResult()
    {
        return array(
            'status'  => $this->status,
            'message' => $this->message
        );
    }

}
