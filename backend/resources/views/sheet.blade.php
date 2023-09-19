<!DOCTYPE html>
<html>

<head>
    <title>Table</title>
</head>

<body>
    <table border="1" style="border-collapse: collapse; width: 100%; text-align: center; border: 1px solid black;">
        <tr style="color: red;">
            <td style="padding: 5px;">Tablea T1</td>
            <td style="padding: 5px;"></td>
            <td style="padding: 5px; color: rgb(27, 101, 150);">Données</td>
        </tr>
        <tr style="color: red;">
            <td style="padding: 4px;"><p>Procesus métier, domaine applicatif ou
                domaine d'activité
                Service: commuus particulariser
            </p>
           <p> Type d'actifs=> </p>
            </td>
            <td style="padding: 5px;">FONCTION (descriptif)</td>
            <td style="padding: 0px;">
                <table border="1" style="border-collapse: collapse; width: 100%; height: 100px; text-align: center; border: 1px solid black;">
                    <tr style="color: rgb(27, 101, 150); width: 20%;">
                        @foreach ($tableData1 as $actif)
                        <td style="padding: 5px;">{{ $actif->Nom }}</td>
                        @endforeach
                    </tr>
                   
                    <tr>
                        <table border="1" style="border-collapse: collapse; width: 100%; height: 100px;  text-align: center; border: 1px solid black;">
                            <tr>
                        @foreach ($filteredData as $actif2)
                        <td style="padding: 5px; color: rgb(27, 101, 150);">{{ $actif2->Code }}</td>
                        @endforeach
                        <td style="padding: 6px;"></td>
                    </tr>
                        <tr>
                        @foreach ($filteredData as $actif3)
                        <td style="padding: 5px; color: rgb(27, 101, 150);">{{ $actif3->Word }}</td>
                        @endforeach
                        <td style="padding: 6px;"></td>
                    </tr>
                        </table>
                    </tr>
                </td>
                    @foreach ($ProcessData as $actif4)
                    <tr style="padding: 4px;">
                        <td ><input type="text" name="Processus_domaine" value="{{ $actif4->Processus_domaine }}" style="padding: 5px;border: none;"; /></td>
                        <td><input type="text" name="Description" value="{{ $actif4->Description }}" style="padding: 5px;border: none;" /></td>
                       
                            <td>
                                <table border="1" style="border-collapse: collapse;  text-align: center; border: 1px solid black;">
                                    <tr>
                                        @foreach ($filteredData as $actif3)
                                        @php
                                            $value = $actif3->Val;
                                            $backgroundColor = ($value == 2) ? 'blue' : (($value == 3) ? 'yellow' : (($value == 4) ? 'red' : 'white'));
                                        @endphp
                                        <td style="padding: 5px;  ">
                                            <input type="text"  name="Valeur" value="{{ $value }}" style="padding: 5px; border: none; background-color: {{ $backgroundColor }};" />
                                        </td>
                                        
                                    @endforeach
                                    
                                    <td><button onclick="updateRow({{ $actif4->ID }})">Update</button></td>
                    </tr>
                        </table>
                            </td>
                    </tr>
                    @endforeach
                    <?php for ($i = 1; $i <=15 ; $i++): ?>
                    <tr style="padding: 4px;">
                        <td ><input type="text" name="inputField" value="" style="padding: 5px;border: none;"; /></td>
                        <td><input type="text" name="inputField" value="" style="padding: 5px;border: none;" /></td>
                       
                      
                        <td>
                            <table border="1" style="border-collapse: collapse;  text-align: center; border: 1px solid black;">
                                <tr>
                    @foreach ($filteredData as $actif3)
                    <td style="padding: 5px; color: rgb(27, 101, 150);"><input type="text" name="inputField" value="" style="padding: 5px;border: none;" /></td>
                    @endforeach
                    <td><button onclick="">Update</button></td>
                </tr>
                    </table>
                        </td>
                    
                      
                    </tr>
                    <?php endfor; ?>
                </table>
            </td>
            </td>
            
        </tr>
       
    </table>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
  
      function updateRow(rowId) {
  
      /*   // Get values from inputs 
        var Processus_domaine = $('input[name="Processus_domaine"]').val();
        var Description = $('input[name="Description"]').val();
        var valeur = $('input[name="Valeur"]').val();
        console.log('Processus:', Processus_domaine);
        console.log('Description:', Description);
        console.log('Valeur:', valeur);
        // AJAX request
        $.ajax({
           url: "/update/"+rowId,
           type: 'PUT',
           data: {
              _token: "{{csrf_token()}}",
              Processus_domaine: Processus_domaine, 
              Description: Description,
              valeur: valeur
           },
           success: function(response) {
              alert('Updated successfully');
           }
        }); */
  
      }
  
    </script>
  
</body>

</html>
