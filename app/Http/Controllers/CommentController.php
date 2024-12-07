<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{

    public function post_comment(Request $request)
{
    $user = auth()->user();

    // if (!$user) {
    //     return response()->json(['error' => 'Bạn cần đăng nhập để bình luận'], 401);
    // }

    // Tìm sản phẩm dựa trên product_id từ request
    $product = Product::find($request->product_id);

    if (!$product) {
        return response()->json(['error' => 'Sản phẩm không tồn tại'], 404);
    }

    // Tạo bình luận mới
    $comment = new Comment();
    $comment->product_id = $product->id;
    $comment->user_id = $user->id;
    $comment->fill($request->except('product_id'));
    $comment->save();

    return response()->json([
        'message' => 'Đăng bình luận thành công, cảm ơn bạn đã góp ý cho sản phẩm chúng tôi',
        'comment' => $comment
    ], 201);
}
public function get_comment()
{
    $user = auth()->user();
    $comments = Comment::orderBy('id', 'DESC')->get();

    return response()->json($comments, 200);
}
public function update_comment(Request $request, $id)
{
    $user = auth()->user();
    // Tìm kiếm bình luận theo ID
    $comment = Comment::find($id);

    // Nếu không tìm thấy bình luận, trả về lỗi 404
    if (!$comment) {
        return response()->json(['error' => 'Bình luận không tồn tại'], 404);
    }

    // Kiểm tra xem có yêu cầu thay đổi nội dung bình luận không
    if ($request->has('comment')) {
        $comment->comment = $request->input('comment'); // Cập nhật nội dung bình luận
    }

    // Kiểm tra và thay đổi trạng thái bình luận nếu có
    // if ($request->has('status')) {
    //     $comment->status = $request->input('status') == 'is_hide' ? 'is_show' : 'is_hide'; // Đổi trạng thái
    // }

    // Lưu lại thay đổi
    $comment->save();

    // Trả về phản hồi JSON với thông báo và bình luận đã được cập nhật
    return response()->json([
        'message' => 'Cập nhật bình luận thành công',
        'comment' => $comment
    ], 200);
}
public function delete_comment($id)
{
    $user = auth()->user();
    // Tìm kiếm bình luận theo ID
    $comment = Comment::find($id);

    // Nếu không tìm thấy bình luận, trả về lỗi 404
    if (!$comment) {
        return response()->json(['error' => 'Bình luận không tồn tại'], 404);
    }

    // Xóa bình luận
    $comment->delete();

    // Trả về phản hồi thành công
    return response()->json([
        'message' => 'Bình luận đã được xóa thành công'
    ], 200);
}


}
