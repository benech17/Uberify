let rec flatten l = match l with
  | [] -> []
  | e :: l ->  e @ flatten l;;
