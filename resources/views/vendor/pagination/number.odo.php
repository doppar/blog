#if ($paginator->hasPages())
    <nav aria-label="Pagination">
        <ul>
            #if ($paginator->onFirstPage())
                <li><span aria-disabled="true">« Previous</span></li>
            #else
                <li><a href="[[ $paginator->previousPageUrl() ]]" rel="prev">« Previous</a></li>
            #endif

            #foreach ($paginator->numbers() as $page)
                #if (is_string($page))
                    <li><span>…</span></li>
                #elseif ($page == $paginator->currentPage())
                    <li><span aria-current="page">[[ $page ]]</span></li>
                #else
                    <li><a href="[[ $paginator->url($page) ]]">[[ $page ]]</a></li>
                #endif
            #endforeach

            #if ($paginator->hasMorePages())
                <li><a href="[[ $paginator->nextPageUrl() ]]" rel="next">Next »</a></li>
            #else
                <li><span aria-disabled="true">Next »</span></li>
            #endif
        </ul>
    </nav>
#else
    <div></div>
#endif
