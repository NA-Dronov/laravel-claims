<th scope="col"@isset($classes) class="{{$classes}}" @endisset>
    <a 
        href="{{
            route('claims.index', 
                array_merge($search, 
                    [
                        'sort_by' => $field_name, 
                        'sort_order' => !empty($sorting['sort_by']) && $sorting['sort_by'] == $field_name ? ($sorting['sort_order'] == 'desc' ? 'asc' : 'desc') : 'desc'
                    ]
                )
            )
        }}"
        class="table-link"
    ><span class="table-link-text">{{$field_desc}}</span> 
    @if (!empty($sorting['sort_by']) && $sorting['sort_by'] == $field_name && $sorting['sort_order'] == 'desc')
    <i class="fas fa-sort-up"></i>
    @elseif (!empty($sorting['sort_by']) && $sorting['sort_by'] == $field_name && $sorting['sort_order'] == 'asc')
    <i class="fas fa-sort-down"></i>
    @else
    <i class="fas fa-sort"></i>
    @endif 
    </a>
</th>