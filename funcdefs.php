<?php
const STYLE_TOKENS=['__','**','//'];
const LINK_TOKEN=["-[","]-"];
const BR="<br>";

function printError($error) {
		if (!empty($error)) {
				echo '<small class="error">' .$error. '</small><br />';
		}
}

function initdb(){
global $db;
global $dbusr;
if (file_exists('wiki.db') and file_exists('users.db')){
    $ftxt=file_get_contents('wiki.db');
    $uftxt=file_get_contents('users.db');

    $db=unserialize($ftxt);
    $dbusr=unserialize($uftxt);
  }else{
    printError('A .db file is missing or empty');

  }
}

function savedb(){
  global $db;
  global $dbusr;
  file_put_contents('wiki.db',serialize($db));
  file_put_contents('users.db',serialize($dbusr));
}

function newuser($uname,$upwd){
  global $dbusr;
  foreach($dbusr as $uar){
    if ($uar['n']==$uname){
      printError("user $uname already exists");
      return null;
    }
  }

  $dbusr[]=["n" => $uname, "p" => password_hash($upwd, PASSWORD_DEFAULT),'op' => [], 'id'=>sizeof($dbusr)];// id should be the index of user INTO $dbusr
  echo "<strong>registration sucessful</strong>";
  savedb();
}

function verifylogin($user,$enteredpass){
  global $dbusr;
  foreach ($dbusr as $u){
    if ($u['n']==$user){
      $storedhash=$u['p'];
      break;
    }
  }
  if($storedhash==null){
    printError('user not found');
    return null;
  }
  if(password_verify($enteredpass,$storedhash)) {
    setcookie('verified', $user,0);
    echo '<p class="notify">You are now logged in.</p>';
  }else {
    printError('Invalid password.');
    return null;
  }
}

function newpage($ttl,$txt){
  global $db;
  global $dbusr;
  $pageindex=count($db);
  foreach($db as $p){
    if ($p['t']==$ttl){
      printError("page $ttl already exists in the main DB.");
      return null;
    }  
  }
  foreach($dbusr as $k=>$u){
    if($_COOKIE['verified'] == $u['n']){ //im ugly fixme pls
      array_push($dbusr[$k]['op'],$pageindex);
    }
  }  
  array_push($db, ['t' => $ttl, 'c' => $txt, "a"=>$_COOKIE['verified'],"ts"=>date('jS \of F Y h:i:s A'),"com"=>[] ]);
  savedb();
}

function editpagearray($index,$newarr){
  global $db;
  // TODO
}

function showtitles(){
//fixme : most recent notes should be on top
// while older are to bottom XXX
  global $db;
  foreach ($db as $k=>$p){
    // TODO XXX below em cnt. author should be <a> link. to author(user) profile
    echo '<a href="/view.php?index='.$k.'">'.$p['t'].'</a>'."<em> By : {$p['a']}</em><br>";
  }
}

//TODO make show last 5 pages function


function viewpage($ind){
  global $db;
  $p=$db[$ind];
  echo "<h1>{$p['t']}</h1>";
  echo "<em class=\"auth\">written by : {$p['a']}</em><br>";
  echo "<em class=\"time\">at : {$p['ts']}</em><br>";
  $lines=explode("\n",$p['c']);
  foreach($lines as $l){
    if($l[0]=="#" and $l[1]==" "){
      echo '<h3>'.$l.'</h3>'.'<br>';  
    }
    echo '<p>'.parseline($l).'</p>'.'<br>';  
  }
  echo '<h3>'.count($p['com']).' comments:</h3>';
  echo '<div class="comments_box">';
  foreach($p['com'] as $c){
    echo '<div class="comment">';
    echo '<u class="com_auth">'.$c['a'].'</u><br>'.'<br><small class="com_tstamp">'.$c['ts'].'</small>';
    echo '<p class="com">'.$c['c'].'</p>';
    echo '</div>';
     
  }
  echo '</div>';
}

function viewprofile($uindex){
  // TODO output html stuff pertaining to profile
}



function parseline($l){
  // parse single line for markup
  // replacing string with html if markup is found
  // finally return modified line to b displayed
  foreach(STYLE_TOKENS as $st){
      $startpos=strpos($l,$st);
      $endpos=strpos($l,$st,$startpos+2);
      if($startpos and $endpos){
        $midstr=substr($l,$startpos+2,$endpos-($startpos+2));
        $needle=$st.$midstr.$st;
        switch ($st){
        case '__':
          $l=str_replace($needle,"<u>$midstr</u>",$l); 
          break;
        case '**':
          $l=str_replace($needle,"<b>$midstr</b>",$l); 
          break;
        case '//':
          $l=str_replace($needle,"<em>$midstr</em>",$l); 
      }
    }
  }
  $linkstartpos=strpos($l,LINK_TOKEN[0]);
  $linkendpos=strpos($l,LINK_TOKEN[1]);
  if($linkstartpos and $linkendpos){
    $midstr=substr($l,$linkstartpos+2,$linkendpos-($linkstartpos+2));
    $needle=LINK_TOKEN[0].$midstr.LINK_TOKEN[1];
    $l=str_replace($needle,'<a class="link" href="?index='.getindexfromtitle($midstr).'">'."$midstr</a>",$l); 
  }
  return $l;
}

function addcomment($pindex,$comtext){
  global $db;
  // todo : check if pindex exists inside db first !
  $db[$pindex]['com'][]=['a'=>$_COOKIE['verified'],'c'=>$comtext,'ts'=>date('jS \of F Y h:i:s A')];
  savedb();
    
}

function getindexfromtitle($ttl){
  global $db;
  foreach ($db as $k=>$p){
    if($p['t'] == $ttl){
      return $k;
    }
  }
  return null;
}

function gettitlefromindex($ind){
  global $db;
  return $db[$ind]['t'];
}

// TODO make getusername from index func

function getindexfromusername($uname){
  global $dbusr;
  foreach ($dbusr as $k=>$u){
    if($u['n'] == $uname){
      return $k;
    }
  }
  return null;
}
?>
