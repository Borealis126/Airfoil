<!--Declare static variables, as well as handle form/click input-->    
    <?php
        //Declare number of levels above and below to display.
            $LevelsAbove=2;
            $LevelsBelow=1;
        //Set CurrentFolder as Home by default.
            $CurrentFolder="Home";
        //If a piece of text was just clicked...
            if(!is_null($FolderRequest=$_REQUEST['clicked']))
                {
                    $CurrentFolder=$FolderRequest;
                    $StoredFolder=$_REQUEST['StoredFolder'];
                }
        //Otherwise, if a form was just submitted...
            elseif(!empty($_POST['CurrentFolder']))
                {
                $CurrentFolder=$_POST['CurrentFolder'];
                $StoredFolder=$_POST['StoredFolder'];
                }
        //If the NewDirectory button was just pressed
            if(!is_null($_POST['NewDirectory']))
                {
                //Count the number of folders in the directory
                    $dir=glob($CurrentFolder."/*",GLOB_ONLYDIR);
                    $NumFolders=count($dir);
                    $NewNumFolders=$NumFolders+1;
                    $NewFolder="$CurrentFolder/$NewNumFolders";
                //Make a new folder with a text file in it called "Text.txt" and one called "Collapsed.txt"
                    mkdir($NewFolder);
                    $NewFile = fopen("$NewFolder/Text.txt", "w");
                    fwrite($NewFile, "NewLine");
                    $NewFile = fopen("$NewFolder/Collapsed.txt", "w");
                    fwrite($NewFile, "false");
            }
        //If the DeleteDirectory button was just pressed
            if(!is_null($_POST['DeleteDirectory']))
                {
                    $ParentDirectory=dirname($CurrentFolder);
                //Delete the Directory
                    deleteDir($CurrentFolder);
                //Rename the remaining directories.
                    $Children = array_filter(glob($ParentDirectory.'/*'), 'is_dir');
                        for($iterator=1;$iterator<=sizeof($Children);$iterator++)
                        {
                            $iteratorchangeindex=$iterator-1;
                            rename("$Children[$iteratorchangeindex]","$ParentDirectory/$iterator");
                        }
            }
        //If the home button was just pressed
            if(!is_null($_POST['BackToHome']))
                {
                //Set home as the current folder
                    $CurrentFolder="Home";
            }
        //If a value for newposition was just input
            if(isset($_POST['PositionSubmit']))
                {
                        $newposition=$_POST['newposition'];
                    //Get the parent directory.
                        $ParentFolder=dirname($CurrentFolder);
                    //Get the parent's children.
                        $Children = array_filter(glob($ParentDirectory.'/*'), 'is_dir');
                    //Figure out what number CurrentFolder is
                        for($iterator=1;"$ParentFolder/$iterator"!="$CurrentFolder";$iterator++);
                        $CurrentFolderNumber=$iterator;
                    //Swap until it's in the right position.
                    if($newposition>$CurrentFolderNumber)
                        $increment=1;
                    else
                        $increment=-1;
                    for($iterator=$CurrentFolderNumber;$iterator!=$newposition;$iterator+=$increment)
                    {
                        //Swap iterator and iterator-1
                        $iteratorplusincrement=$iterator+$increment;
                        rename("$ParentFolder/$iteratorplusincrement","$ParentFolder/temp");
                        rename("$ParentFolder/$iterator","$ParentFolder/$iteratorplusincrement");
                        rename("$ParentFolder/temp","$ParentFolder/$iterator");
                        
                    }
                    $CurrentFolder="$ParentFolder/$newposition";
                }
        //If text was just submitted, write it to the appropriate file.
            if (isset($_POST['TextSubmit']))
                {
                $TextEntry=$_POST["textentry"];
                $myfile = fopen("$CurrentFolder/Text.txt", "w");
                fwrite($myfile, $TextEntry);
                fclose($myfile);
                }
        //If a folder was just grabbed.
            if (isset($_POST['FolderGrabbed']))
                {
                    //Store the name of the current folder.
                        $StoredFolder=$CurrentFolder;
                }
        //If a folder was just dropped.
            if (isset($_POST['FolderDropped']))
                {
                        $StoredFolder=$_POST['StoredFolder'];
                    //Store the parent of the stored folder.
                        $ParentStoredFolder=dirname($StoredFolder);    
                    
                    //Make StoredFolder a child of CurrentFolder
                        //Count the number of folders in the directory
                            $dir=glob($CurrentFolder."/*",GLOB_ONLYDIR);
                            $NumFolders=count($dir);
                            $NewNumFolders=$NumFolders+1;
                            $NewFolder="$CurrentFolder/$NewNumFolders";
                        //Rename the grabbed directory as the next child in the folder
                            rename($StoredFolder,$NewFolder);
                    //Now go back and rename the directories where the stored folder used to be.
                        $Children = array_filter(glob($ParentStoredFolder.'/*'), 'is_dir');
                        for($iterator=1;$iterator<=sizeof($Children);$iterator++)
                        {
                            $iteratorchangeindex=$iterator-1;
                            rename("$Children[$iteratorchangeindex]","$ParentStoredFolder/$iterator");
                        }
                        
                }
        //If a folder was just collapsed
            if (isset($_POST['FolderCollapsed']))
                {
                    $File = fopen("$CurrentFolder/Collapsed.txt", "w");
                    fwrite($File, "true");
                }
        //If a folder was just expanded
            if (isset($_POST['FolderExpanded']))
                {
                    $File = fopen("$CurrentFolder/Collapsed.txt", "w");
                    fwrite($File, "false");
                }
        //If $CurrentFolder no longer exists as a folder, set it back to home.
            if(!is_dir($CurrentFolder))
                $CurrentFolder="Home";
        //If $GrabbedFolder no longer exists as a folder, empty it.
            if(!is_dir($StoredFolder))
                $StoredFolder='';
        //Echo the current and grabbed folders.
            echo "Current Folder: $CurrentFolder";
            echo "<br>Grabbed Folder: $StoredFolder";
    ?>


<!--Static HTML forms-->
    <!DOCTYPE HTML>
    <html>
	<head>
		<meta charset="utf-8">
		<title>Information Hierarchy</title>
		<link rel="stylesheet" href="styles2.css">
		<script type="text/x-mathjax-config">
        MathJax.Hub.Config({tex2jax: {inlineMath: [['$','$'], ['\\(','\\)']]}});
        </script>
        <script type='text/javascript' src='https://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML'>
    </script>
	</head>
	<body
	    <div class="wrapper">
	    <!--Text box at the top of the screen for text entry, directory creation, and directory removal.-->
	    <form action="index.php" method="post">
	        <input type="submit" name="BackToHome" value="Home"/>
	        <span id="inputtext">Input Text: </span>
	        <textarea class="InputTextBox" name="textentry"><?php echo file_get_contents("$CurrentFolder/"."Text.txt");?></textarea>
	        <input type="submit" name="TextSubmit" value="Submit"/>
	        <span id="newpositiontext">New Position: </span>
	        <textarea class="PositionTextBox" name="newposition"></textarea>
	        <input type="submit" name="PositionSubmit" value="Submit"/>
	        <input type="submit" id="NewDirectory" name="NewDirectory" value="New Directory"/>
	        <input type="submit" name="DeleteDirectory" value="Delete Directory"/>
	        <input type='hidden' name='CurrentFolder' value="<?php echo $CurrentFolder;?>"/>
	        <input type='hidden' name='StoredFolder' value="<?php echo $StoredFolder;?>"/>
	        <input type="submit" id="grab" name="FolderGrabbed" value="Grab"/>
	        <input type="submit" name="FolderDropped" value="Drop"/>
	        <input type="submit" id="collapse" name="FolderCollapsed" value="Collapse"/>
	        <input type="submit" name="FolderExpanded" value="Expand"/>
	    </form>
	    </div>
	</body>
</html>

<!--Display the hierarchy-->
    <?php
            //Find and use a suitable root directory for the recursive child call. 
                $RootFolder=$CurrentFolder;
                $iterator=0;
                for($iterator=0;$iterator<=$LevelsAbove and $RootFolder!="Home";$iterator++)
                    $RootFolder=dirname($RootFolder);
                RecursiveBullets($RootFolder,-1,true,$iterator)
    ?>


<!--Function Declarations-->
    <?php
        //This is the function that creates a certain bullet point.
            function CreateBullet($level,$Content,$Directory)
            {
                //If this is the CurrentFolder, make it a unique level
                    global $CurrentFolder;
                    global $StoredFolder;
                    if($Directory==$CurrentFolder)
                        $level="$level"." Current";
                    $link="index.php?clicked=$Directory&StoredFolder=$StoredFolder";
                
                    echo    "<ul>".
                            "<li class=\"level$level\">".
                            "<a href=$link>$Content</a>".
                            '</li>'.
                            "</ul>";
            }
        //This function prints out the text in a given folder, and all of its child folders, down 2 levels.
            function RecursiveBullets($Folder,$level,$Grandparent,$numabove)
            {       
                    //Output the text of this folder, unless it is the Grandparent
                        if(!$Grandparent)
                        {
                            $Data=file_get_contents("$Folder/"."Text.txt");
                            CreateBullet($level,$Data,$Folder);
                        }
                    //Iterate over subfolders as long as the folder is not collapsed.
                        $Children = array_filter(glob($Folder.'/*'), 'is_dir');
                        
                        $Collapsed=file_get_contents("$Folder/"."Collapsed.txt");
                        if($Collapsed=="false")
                        {
                            foreach($Children as $NextChild)
                            {
                                global $LevelsAbove;
                                global $LevelsBelow;
                                if($level<$LevelsBelow-$LevelsAbove+$numabove+1)
                                    RecursiveBullets("$NextChild",$level+1,false,$numabove);
                            }
                        }
            }
        //Delete directory recursively.
            function deleteDir($path) 
            {
                if (empty($path)) { 
                    return false;
                }
                return is_file($path) ?
                        @unlink($path) :
                        array_map(__FUNCTION__, glob($path.'/*')) == @rmdir($path);
            }
    ?>

