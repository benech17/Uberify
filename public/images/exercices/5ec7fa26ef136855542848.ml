let rec map f l = match l with
  | [] -> []
  | e :: l -> [f e] @ (map f l) ;;