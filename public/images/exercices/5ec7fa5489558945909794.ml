let rec filter p l = match l with 
  | [] -> []
  | e :: l -> if p e then e :: filter p l else filter p l ;;