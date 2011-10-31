    <?php

        // Set up some variables to easily refer to particular fields you've configured
        // in $config['skylight_searchresult_display']

        $title_field = $this->skylight_utilities->getField('Title');
        $author_field = $this->skylight_utilities->getField('Author');
        $date_field = $this->skylight_utilities->getField('Date');

        $base_parameters = preg_replace("/[?&]sort_by=[_a-zA-Z+%20. ]+/","",$base_parameters);
        if($base_parameters == "") {
            $sort = '?sort_by=';
        }
        else {
            $sort = '&sort_by=';
        }
    ?>
    <div class="listing-filter">
        <span class="no-results">
        <strong><?php echo $startrow ?>-<?php echo $endrow ?></strong> of
            <strong><?php echo $rows ?></strong> results
        </span>

        <span class="sort">
            <strong>Sort by</strong>
            <?php foreach($sort_options as $label => $field) { ?>
                <em><?php echo $label ?></em>
                <?php if($label != "Date") { ?>
                <a href="<?php echo $base_search.$base_parameters.$sort.$field.'+asc' ?>">A-Z</a> |
                <a href="<?php echo $base_search.$base_parameters.$sort.$field.'+desc' ?>">Z-A</a>
            <?php } else { ?>
                <a href="<?php echo $base_search.$base_parameters.$sort.$field.'+desc' ?>">newest</a> |
                <a href="<?php echo $base_search.$base_parameters.$sort.$field.'+asc' ?>">oldest</a>
          <?php } }  ?>
            
        </span>

    </div>


    <ul class="listing">

       
    <?php foreach ($docs as $doc) { ?>

            <?php
                $image = '';
                $type = 'Unknown';
                $typeField = 'dctype';
                $searchResultFields = $this->config->item('skylight_searchresult_display');
                if(isset($searchResultFields['Type'])) {
                    $typeField = $searchResultFields['Type'];
                }

                if(array_key_exists($typeField, $doc)) {
                        $type = implode(" ",$doc[$typeField]);
                }

                if($display_thumbnail && array_key_exists($thumbnail_field, $doc)) {

                        $image = getBitstreamUri($doc[$thumbnail_field][0]);
                }
                else if (file_exists('./assets/images/'.strtolower($type).'.png')) {
                       $image = './assets/images/'.strtolower($type).'.png';
                }
                else {
                    $image = './assets/images/unknown.png';
                }

            ?>

    <li>
        <span class="icon media-doc"></span>
        <h3><a href="./record/<?php echo $doc['id']?>?highlight=<?php echo $query ?>"><?php echo $doc[$title_field][0]; ?></a></h3>
        <?php if(array_key_exists($author_field,$doc)) { ?>
        <span class="authors">
            <?php

            $num_authors = 0;
            foreach ($doc[$author_field] as $author) {
               // test author linking
               // quick hack that only works if the filter key
               // and recorddisplay key match and the delimiter is :
               $orig_filter = preg_replace('/ /','+',$author, -1);
               $orig_filter = preg_replace('/,/','%2C',$orig_filter, -1);
               echo '<a class=\'filter-link\' href=\'./search/*/Author:"'.$orig_filter.'"\'>'.$author.'</a>';
                $num_authors++;
                if($num_authors < sizeof($doc[$author_field])) {
                    echo '; ';
                }
            }


            ?>
        </span>
            <?php } ?>
       
        <em>
       <?php if(array_key_exists($date_field, $doc)) { ?>
            <span class="date">
                <?php
                echo '(' . $doc[$date_field][0] . ')';
          }
                    elseif(array_key_exists('dateIssuedyear', $doc)) {
                        echo '( ' . $doc['dateIssuedyear'][0] . ')';
                    }

                ?>
                </span>
        </em>
        <br/>
        


        <?php
        // TODO: Make highlighting configurable

        if(array_key_exists('highlights',$doc)) {
            ?> <p class="abstract"><?php
            foreach($doc['highlights'] as $highlight) {
                echo "...".$highlight."...".'<br/>';
            }
            ?></p><?php
        }
        else {
            if(array_key_exists('dcdescriptionabstracten', $doc)) {
                echo '<p class="abstract">';
                $abstract =  $doc['dcdescriptionabstracten'][0];
                $abstract_words = explode(' ',$abstract);
                $shortened = '';
                $max = 40;
                $suffix = '...';
                if($max > sizeof($abstract_words)) {
                    $max = sizeof($abstract_words);
                    $suffix = '';
                }
                for ($i=0 ; $i<$max ; $i++){
                    $shortened .= $abstract_words[$i] . ' ';
                }
                echo $shortened.$suffix;
                echo '</p>';
            }
        }

        ?>


        <p class="read_item"><a class="record_list_links"  href="./record/<?php echo $doc['id']?>">Read more...</a></p>
    </li>
    <?php

        if(array_key_exists('exifgpscoordinates', $doc)) {
            $coordinates[$doc['id']] = $doc['exifgpscoordinates'];
        }

    } ?>
    </ul>

    <div class="pagination">
       <?php echo $pagelinks ?>
    </div>