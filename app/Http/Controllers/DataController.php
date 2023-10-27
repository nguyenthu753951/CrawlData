<?php

namespace App\Http\Controllers;

use Goutte\Client;
use Nette\Utils\Arrays;
use Symfony\Component\DomCrawler\Crawler;

class DataController extends Controller
{
    public function crawlData()
    {
        $response = \Http::get('https://dantri.com.vn/giao-duc/vu-lo-de-thi-sinh-8-thi-sinh-duoc-mom-de-can-xu-ly-the-nao-20230620004656478.htm');
        $dom = new \DOMDocument();
        @$dom->loadHTML(mb_convert_encoding((string) $response->body(), 'HTML-ENTITIES',  'UTF-8'));

        //Dùng để query tag, class, id của 1 thẻ HTML
        $xpath = new \DOMXPath($dom);

        // lấy data từ phần Đọc nhiều trong giáo dục
        $education_news_images_query = $xpath->query('.//article //article[contains(@class, "article-item")] //div[contains(@class, "article-thumb")] //a //img');
        $education_news_titles = $xpath->query('.//article //article[contains(@class, "article-item")] //h3 //a');
        foreach ($education_news_images_query as $key => $image) {
            $data[] = ['image' => $image->getAttribute("data-src"),
                'link' => $education_news_titles[$key] ? $education_news_titles[$key]->getAttribute("href") : '',
                'title' => $education_news_titles[$key] ? $education_news_titles[$key]->textContent : '',
            ];
        }

        // lấy data từ phần Tin liên quan
        $related_news_images_query = $xpath->query('.//aside //article[contains(@class, "article-item")] //div[contains(@class, "article-thumb")] //a //img');
        $related_news_titles = $xpath->query('.//aside //article[contains(@class, "article-item")] //div[contains(@class, "article-content")] //h3 //a');
        $related_news_descriptions = $xpath->query('.//aside //article[contains(@class, "article-item")] //div[contains(@class, "article-excerpt")] //a');
        foreach ($related_news_images_query as $key => $image) {
            $data[] = ['image' => $image->getAttribute("data-src"),
                'link' => $related_news_titles[$key] ? $related_news_titles[$key]->getAttribute("href") : '',
                'title' => $related_news_titles[$key] ? $related_news_titles[$key]->textContent : '',
                'description' => $related_news_descriptions[$key] ? $related_news_descriptions[$key]->textContent : '',
            ];
        }

        //lấy data từ phần ĐANG ĐƯỢC QUAN TÂM
        $interested_news_images_query = $xpath->query('.//div[contains(@class, "body-container")] //div[contains(@class, "grid-container")]
        //div[contains(@class, "singular-wrap")] //div//div//div[contains(@class, "lazyload-wrapper")]');
        // dd($interested_news_images_query);

        $interested_news_titles = $xpath->query('*//div[contains(@class, "article-care")] //article[contains(@class, "article-item")] //div[contains(@class, "article-content")] //h3 //a');
        $interested_news_descriptions = $xpath->query('*//div[contains(@title, "Đang được quan tâm")] //div[contains(@class, "lazyload-wrapper")] //div[contains(@class, "article-care")] //article[contains(@class, "article-item")] //div[contains(@class, "article-excerpt")] //a');
        foreach ($interested_news_images_query as $key => $image) {
            $data[] = ['image' => $image->getAttribute("data-src"),
                'link' => $interested_news_titles[$key] ? $interested_news_titles[$key]->getAttribute("href") : '',
                'title' => $interested_news_titles[$key] ? $interested_news_titles[$key]->textContent : '',
                'description' => $interested_news_descriptions[$key] ? $interested_news_descriptions[$key]->textContent : '',
            ];
        }

        return ($data);
    }
}
