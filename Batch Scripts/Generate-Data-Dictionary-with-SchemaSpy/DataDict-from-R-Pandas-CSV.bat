pip install csvs-to-sqlite
csvs-to-sqlite c:\datadict\<dataset_name>\csv c:\datadict\<dataset_name>.db


java -Djava.library.path=c:\datadict\schemaspy\lib -jar "C:\datadict\schemaspy\schemaspy-6.2.4.jar" -dp "C:\datadict\schemaspy\lib\sqlite-jdbc-3.45.2.0.jar" -t sqlite-xerial -desc "<dataset description>" -o "c:\datadict\html\<dataset_description>" -ahic  -norows  -lq  -nologo -db "c:\datadict\<dataset_name>.db" -all -sso
