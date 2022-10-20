<?php


$q = mysqli_query($con,"SELECT * FROM candidate WHERE position_name = '".$_POST['pos']."'");

echo '<table border="0" width="620" align="center">
<CAPTION><h3>Available Candidates for vote</h3></CAPTION>
  <tr>
    <th>Candidate Name</th>
    <th>Candidate Position</th>
    <th>Vote/No Vote</th>
  </tr>';
  
while($row = mysqli_fetch_assoc($q)){
  
  echo '<tr>
    <td>'.$row["candidate_name"].'</td>
    <td>'.$row["candidate_position"].'</td>';
    if($row['voteflag']!=1) 
           echo '<td><a href="vote.php?vote='.$row["candidate_id"].'"> Please Vote </a></td>';
            else echo "<td>You have already voted..</td>";
  echo '</tr>';
  
  }

echo '</table>';

?>