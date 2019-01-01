
<html>
<head>
<link rel="stylesheet" href="CSS/Default.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js" type="text/javascript"></script>

</head>

<body>
    <h1><u><font size="5">Ledger Program</font></u></h1>
     <table border="1">
    <tr>
        <td>
           <p>
           Information:
            <br>This is a program designed to aid in the understanding of how you add to the blockchain ledger.
            </p>
       
       </td>
   </tr>
   </table>
   <br><br>
    <!-- FORM INPUT HERE -->    
    <form id = "myForm" action="LedgerProgramV4.php" method="post">
    Blockchain Name: <textarea id="FileName" name = "FileName" rows="1" cols="10" title="Enter in a File Name Here."><?php if(isset($_POST['FileName'])) { 
         echo htmlentities ($_POST['FileName']); }?></textarea><br>
     Sender: <textarea id="SenderAddr" name = "SenderAddr" rows="1" cols="10" title="Enter in sender Name."><?php if(isset($_POST['SenderAddr'])) { 
         echo htmlentities ($_POST['SenderAddr']); }?></textarea><br>
     Amount: <textarea id="Amount" name = "Amount" rows="1" cols="3" title="Enter in a Amount." ><?php if(isset($_POST['Amount'])) { 
         echo htmlentities ($_POST['Amount']); }?></textarea><br>
     Receiver: <textarea id="ReceiverAddr" name = "ReceiverAddr" rows="1" cols="10" title="Enter in a Receiver Name." ><?php if(isset($_POST['ReceiverAddr'])) { 
         echo htmlentities ($_POST['ReceiverAddr']); }?></textarea><br>     
    Nonce: <textarea id="Nonce" name = "Nonce" rows="1" cols="3" title="Enter in a nonce value here to calculate the hash."><?php if(isset($_POST['Nonce'])) { 
         echo htmlentities ($_POST['Nonce']); }?></textarea><br>        
    </select>    
    <br>     
    <br><input type="submit">  
    </form>   
    <?php 
        session_start();            
        $FileName = htmlspecialchars($_POST['FileName']);      
        $html = '<div id="Block"><h1>Blockchain Data: </h1></div><br />';    
        echo ($html);          
        if (strLen(GetInputData())>=5){  
            WriteToFile($FileName);
            $FileLinesArray=readfileToArray($FileName.".txt"); 
            printTransactions($FileLinesArray, $FileName);           
        } 
    
        //USER DEFINED FUNCTIONS
        function GetInputData(){
            $Amount = htmlspecialchars($_POST['Amount']);
            $Nonce = htmlspecialchars($_POST['Nonce']);
            $SenderAddr = htmlspecialchars($_POST['SenderAddr']);
            $ReceiverAddr = htmlspecialchars($_POST['ReceiverAddr']);
            
            $DataToReturn = $SenderAddr . " " . $Amount . " " . $ReceiverAddr . " "  . $Nonce;

            return $DataToReturn;
        }

        function GetNewHash($DataToHash, $HashAlg){
            $HashValue = hash($HashAlg, $DataToHash);
            return $HashValue;
        }

        function WriteToFile($FileToOpen2){    
                $DateAndTime = date("Y/m/d"). "@" . date("h:i:sa");
                $ArrayHashAlg = array("sha256");
                $ArrayLengthHashAlg = count($ArrayHashAlg);
                for($i = 0; $i < $ArrayLengthHashAlg; ++$i) {
                    $HashFinalValue = GetNewHash(GetInputData(), $ArrayHashAlg[$i]);      
                }  
                $DataToInsertIntoFile = GetInputData() . " " . $HashFinalValue . " " . $DateAndTime;           
                writeToFileDataPlain($FileToOpen2, $DataToInsertIntoFile);
                if(strlen($HashFinalValue)>=3){
                    writeToFileDataPlain($FileToOpen2."Hash", $HashFinalValue);
                }               
        }
        function writeToFileDataPlain($FileName, $Data){
            $myfile = fopen($FileName.".txt", 'a') or die("Unable to open file!");
            fwrite($myfile, $Data."\r\n");
            fclose($myfile);
        }
        function readfileToArray($fileName){
            $LinesFromFile=array();
            $myfileRead = fopen($fileName, "r") or die("Unable to open file!");            
            while(!feof($myfileRead)) {
                $LineFromFile = fgets($myfileRead, 4096);
                array_push($LinesFromFile, $LineFromFile);    
                }        
                fclose($myfileRead);
                return $LinesFromFile;            
        }

        function printTransactions($ArrayName, $Name){
                $counter = 1;    
                $BlockNumber = 0;
                $HashesFromFileArray = readfileToArray($Name."Hash.txt");
                array_pop($HashesFromFileArray);  
                foreach ($ArrayName as $line){     
                    PrintoutToUser("yellow", "3", "Transaction " . $counter . " <font color='white'>" . $line . "</font>");                                     
                    If ($counter % 2 == 0) {                        
                        $BlockNumber = $BlockNumber + 1;                                
                        if(isset($HashesFromFileArray[$counter -1])=="1" && isset($HashesFromFileArray[$counter -2])=="1" && gettype($HashesFromFileArray[$counter -1])!="NULL"){
                            if(strlen($HashesFromFileArray[$counter -1])>=2 && strlen($HashesFromFileArray[$counter -2])>=2){

                                $HashValue = GetNewHash($HashesFromFileArray[$counter -2].$HashesFromFileArray[$counter -1], "sha256");                             
                                $TransactionInputCounter = $counter-1;
                                $TransactionInputCounter2 = $counter;
                                PrintoutToUser("green", "3", "T". $TransactionInputCounter.":" . $HashesFromFileArray[$counter -2]);
                                PrintoutToUser("green", "3", "T". $TransactionInputCounter2.":" . $HashesFromFileArray[$counter -1]);  
                                PrintoutToUser("blue", "3", "Data Hashed " . $HashesFromFileArray[$counter -2].$HashesFromFileArray[$counter -1]." Length of Array input 1 and 2 is " . strLen($HashesFromFileArray[$counter -2]) . " and " . strLen($HashesFromFileArray[$counter -2]));
                                PrintoutToUser("red", "3", "End of Block: " .$BlockNumber . ":". $HashValue . "<br />");  
                            }                        
                        } 
                        if(isset($HashValue)){
                            writeToFileDataPlain("TESTING", $HashValue); 
                            $HashValue = NULL; 
                        }                                                                 
                    }            
                    $counter = $counter + 1;                     
                } 
            }

        function checkVariableNotEmpty($Var){
                return (isset($Var) && strlen($Var) != 0);
            }
        
        //FUNCTION PRINT OUT DATA
        function PrintoutToUser($FontColor, $FontSize, $DataToPrintOut){
            echo "<font size='". $FontSize ."' color='". $FontColor ."'>" . $DataToPrintOut . "</font> <br />";
        }
    ?>     
<br><a href="index.html"><font color="red">Link Back Home</font></a>
   
</body>
<title>Ledger PROGRAM</title>
</html>