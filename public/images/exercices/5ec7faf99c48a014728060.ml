let rec perfect a = match a with
  | Nil -> true
  | Node(_,x,y) -> if (x=Nil && y=Nil) then true else if (x!=Nil && y!=Nil) then
        if (depth x = depth y) then (perfect x && perfect y) else false 
      else false;;

