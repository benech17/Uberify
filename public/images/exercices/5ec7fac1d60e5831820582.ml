let rec size a = match a with
  | Nil -> 0 
  | Node(_,x,y) -> 1 + size x + size y ;;
